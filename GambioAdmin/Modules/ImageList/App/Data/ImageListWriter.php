<?php
/*--------------------------------------------------------------
   ImageListWriter.php 2023-06-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ImageList\App\Data;

use Doctrine\DBAL\Connection;
use Exception;
use Gambio\Admin\Modules\ImageList\Model\Entities\Image;
use Gambio\Admin\Modules\ImageList\Model\ImageList;
use Gambio\Admin\Modules\ImageList\Model\ValueObjects\ImageAltTitle;
use Gambio\Admin\Modules\ImageList\Model\ValueObjects\ImageListId;
use Gambio\Admin\Modules\ImageList\Model\ValueObjects\ImageListName;
use Gambio\Admin\Modules\ImageList\Model\ValueObjects\ImageTitle;
use Gambio\Admin\Modules\ImageList\Model\ValueObjects\OriginalImagesDirectory;
use Gambio\Admin\Modules\ImageList\Services\Exceptions\CreationOfImageListsFailedException;
use Gambio\Admin\Modules\ImageList\Services\Exceptions\DeletionOfImageListsFailedException;
use Gambio\Admin\Modules\ImageList\Services\Exceptions\ImageDoesNotExistException;
use Gambio\Admin\Modules\ImageList\Services\Exceptions\StorageOfImageListsFailedException;

/**
 * Class ImageListWriter
 *
 * @package Gambio\Admin\Modules\ImageList\App\Data
 */
class ImageListWriter
{
    /**
     * @var Connection
     */
    private $connection;
    
    /**
     * @var OriginalImagesDirectory
     */
    private $imagesDirectory;
    
    /**
     * @var array
     */
    private $languageCodeToIdMap = [];
    
    
    /**
     * ImageListReader constructor.
     *
     * @param Connection              $connection
     * @param OriginalImagesDirectory $imagesDirectory
     */
    public function __construct(
        Connection              $connection,
        OriginalImagesDirectory $imagesDirectory
    ) {
        $this->connection      = $connection;
        $this->imagesDirectory = $imagesDirectory;
    }
    
    
    /**
     * Creates an empty image list.
     *
     * @param ImageListName $imageListName
     *
     * @return int
     * @throws CreationOfImageListsFailedException
     */
    public function createImageList(ImageListName $imageListName): int
    {
        try {
            $this->connection->createQueryBuilder()
                ->insert('product_image_list')
                ->setValue('product_image_list_name', ':product_image_list_name')
                ->setParameter('product_image_list_name', $imageListName->value())
                ->executeQuery();
            
            return (int)$this->connection->lastInsertId();
        } catch (Exception $exception) {
            throw CreationOfImageListsFailedException::becauseOfException($exception);
        }
    }
    
    
    /**
     * Creates multiple empty image list.
     *
     * @param ImageListName ...$imageListNames
     *
     * @return int[]
     * @throws CreationOfImageListsFailedException
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\DBAL\Exception
     */
    public function createMultipleImageLists(ImageListName ...$imageListNames): array
    {
        try {
            $this->connection->beginTransaction();
            
            $result = array_map([$this, 'createImageList'], $imageListNames);
            
            $this->connection->commit();
            
            return $result;
        } catch (CreationOfImageListsFailedException $exception) {
            $this->connection->rollBack();
            
            throw $exception;
        }
    }
    
    
    /**
     * Stores one or more existing image lists.
     *
     * @param ImageList ...$imageLists
     *
     * @throws StorageOfImageListsFailedException
     * @throws \Doctrine\DBAL\Exception
     */
    public function storeImageLists(ImageList ...$imageLists): void
    {
        try {
            $this->connection->beginTransaction();
            
            $toBeDeletedImages = [];
            
            foreach ($imageLists as $imageList) {
                $this->updateImageListName($imageList);
                
                foreach ($this->removeImages($imageList) as $imagePath) {
                    $toBeDeletedImages[] = $imagePath;
                }
                
                $this->updateImages($imageList);
                $this->addImages($imageList);
            }
            
            $this->connection->commit();
            
            $toBeDeletedImages = array_unique(array_filter($toBeDeletedImages));
            array_walk($toBeDeletedImages, [$this, 'deleteImageFromFilesystem']);
        } catch (Exception $exception) {
            $this->connection->rollBack();
            
            throw StorageOfImageListsFailedException::becauseOfException($exception);
        }
    }
    
    
    /**
     * Deletes one or more existing image lists.
     *
     * @param ImageListId ...$imageListIds
     *
     * @throws DeletionOfImageListsFailedException
     * @throws \Doctrine\DBAL\Exception
     */
    public function deleteImageLists(ImageListId ...$imageListIds): void
    {
        try {
            $this->connection->beginTransaction();
            
            foreach ($imageListIds as $imageListId) {
                // determining image ids in list
                $imageIds = $this->connection->createQueryBuilder()
                    ->select('product_image_list_image_id')
                    ->from('product_image_list_image')
                    ->where('product_image_list_id = :product_image_list_id')
                    ->setParameter('product_image_list_id', $imageListId->value())
                    ->executeQuery()
                    ->fetchAllAssociative();
                
                $imageIds = array_map(static function (array $row): int {
                    return (int)$row['product_image_list_image_id'];
                }, $imageIds);
                
                if (count($imageIds)) {
                    $this->connection->createQueryBuilder()
                        ->delete('product_image_list_image_text')
                        ->where('product_image_list_image_id IN (' . implode(', ', $imageIds) . ')')
                        ->executeQuery();
                    
                    $this->connection->createQueryBuilder()
                        ->delete('product_image_list_image')
                        ->where('product_image_list_image_id IN (' . implode(', ', $imageIds) . ')')
                        ->executeQuery();
                }
                
                $this->connection->createQueryBuilder()
                    ->delete('product_image_list')
                    ->where('product_image_list_id = :product_image_list_id')
                    ->setParameter('product_image_list_id', $imageListId->value())
                    ->executeQuery();
            }
            
            $this->connection->commit();
        } catch (Exception $exception) {
            $this->connection->rollBack();
            
            throw DeletionOfImageListsFailedException::becauseOfException($exception);
        }
    }
    
    
    /**
     * @param ImageList $imageList
     *
     * @throws \Doctrine\DBAL\Exception
     */
    private function updateImageListName(ImageList $imageList): void
    {
        $this->connection->createQueryBuilder()
            ->update('product_image_list')
            ->set('product_image_list_name', ':product_image_list_name')
            ->where('product_image_list_id = :product_image_list_id')
            ->setParameter('product_image_list_name', $imageList->name())
            ->setParameter('product_image_list_id', $imageList->id())
            ->executeQuery();
    }
    
    
    /**
     * @param ImageList $imageList
     *
     * @return array
     * @throws \Doctrine\DBAL\Exception
     */
    private function removeImages(ImageList $imageList): array
    {
        $relativePaths = [];
        
        foreach ($imageList->images() as $image) {
            $relativePaths[] = $image->relativePath();
        }
        
        $relativePaths = array_map(static function (string $path): string {
            return "'" . $path . "'";
        }, $relativePaths);
        
        $deleteImageQuery = $this->connection->createQueryBuilder()
            ->delete('product_image_list_image')
            ->where('product_image_list_id = :product_image_list_id')
            ->setParameter('product_image_list_id', $imageList->id());
        
        $deletedImagesQuery = $this->connection->createQueryBuilder()
            ->select('product_image_list_image_local_path')
            ->from('product_image_list_image')
            ->where('product_image_list_id = :product_image_list_id')
            ->setParameter('product_image_list_id', $imageList->id());
        
        if (count($relativePaths) !== 0) {
            $deleteImageQuery->andWhere($where = 'product_image_list_image_local_path NOT IN (' . implode(', ',
                                                                                                          $relativePaths)
                                                 . ')');
            $deletedImagesQuery->andWhere($where);
        }
        
        $deletedImages = $deletedImagesQuery->executeQuery()->fetchAllNumeric();
        $deletedImages = array_column($deletedImages, '0');
        $deleteImageQuery->executeQuery();
        
        $deleteImageTextQuery = '
            DELETE FROM product_image_list_image_text WHERE `product_image_list_image_id` NOT IN (
                SELECT DISTINCT `product_image_list_image_id` FROM `product_image_list_image`
            )
        ';
        
        $this->connection->prepare($deleteImageTextQuery)->executeQuery();
        
        return array_map([$this, 'imageIsNotUsedInProductsOrLists'], $deletedImages);
    }
    
    
    /**
     * @param ImageList $imageList
     *
     * @throws ImageDoesNotExistException
     * @throws \Doctrine\DBAL\Exception
     */
    private function updateImages(ImageList $imageList): void
    {
        foreach ($imageList->images() as $image) {
            $imageId = $this->getImageIdByRelativePathAndListId($imageList->id(), $image);
            
            $this->connection->createQueryBuilder()
                ->update('product_image_list_image')
                ->set('product_image_list_image_sort_order', ':product_image_list_image_sort_order')
                ->setParameter('product_image_list_image_sort_order', $image->sortOrder())
                ->where('product_image_list_image_id = :product_image_list_image_id')
                ->setParameter('product_image_list_image_id', $imageId)
                ->executeQuery();
            
            foreach ($image->titles() as $title) {
                $languageId = $this->getLanguageIdFromLanguageCode($title->languageCode());
                
                $this->connection->createQueryBuilder()
                    ->update('product_image_list_image_text')
                    ->set('product_image_list_image_text_value', ':product_image_list_image_text_value')
                    ->where('language_id = :language_id')
                    ->andWhere('product_image_list_image_id = :product_image_list_image_id')
                    ->andWhere('product_image_list_image_text_type = :product_image_list_image_text_type')
                    ->setParameter('product_image_list_image_text_value', $title->text())
                    ->setParameter('language_id', $languageId)
                    ->setParameter('product_image_list_image_id', $imageId)
                    ->setParameter('product_image_list_image_text_type', ImageTitle::TYPE)
                    ->executeQuery();
            }
            
            foreach ($image->altTitles() as $altTitle) {
                $languageId = $this->getLanguageIdFromLanguageCode($altTitle->languageCode());
                
                $this->connection->createQueryBuilder()
                    ->update('product_image_list_image_text')
                    ->set('product_image_list_image_text_value', ':product_image_list_image_text_value')
                    ->where('language_id = :language_id')
                    ->andWhere('product_image_list_image_id = :product_image_list_image_id')
                    ->andWhere('product_image_list_image_text_type = :product_image_list_image_text_type')
                    ->setParameter('product_image_list_image_text_value', $altTitle->text())
                    ->setParameter('language_id', $languageId)
                    ->setParameter('product_image_list_image_id', $imageId)
                    ->setParameter('product_image_list_image_text_type', ImageAltTitle::TYPE)
                    ->executeQuery();
            }
        }
    }
    
    
    /**
     * @param ImageList $imageList
     *
     * @throws \Doctrine\DBAL\Exception
     */
    private function addImages(ImageList $imageList): void
    {
        foreach ($imageList->newImages() as $newImage) {
            $relativePath = $newImage->relativePath();
            
            $this->connection->createQueryBuilder()
                ->insert('product_image_list_image')
                ->setValue('product_image_list_id', ':product_image_list_id')
                ->setValue('product_image_list_image_local_path', ':product_image_list_image_local_path')
                ->setValue('product_image_list_image_sort_order', ':product_image_list_image_sort_order')
                ->setParameter('product_image_list_id', $imageList->id())
                ->setParameter('product_image_list_image_local_path', $relativePath)
                ->setParameter('product_image_list_image_sort_order', $newImage->sortOrder())
                ->executeQuery();
            
            $imageId = (int)$this->connection->lastInsertId();
            
            foreach ($newImage->titles() as $title) {
                $languageId = $this->getLanguageIdFromLanguageCode($title->languageCode());
                
                $this->connection->createQueryBuilder()
                    ->insert('product_image_list_image_text')
                    ->setValue('language_id', ':language_id')
                    ->setValue('product_image_list_image_id', ':product_image_list_image_id')
                    ->setValue('product_image_list_image_text_type', ':product_image_list_image_text_type')
                    ->setValue('product_image_list_image_text_value', ':product_image_list_image_text_value')
                    ->setParameter('language_id', $languageId)
                    ->setParameter('product_image_list_image_id', $imageId)
                    ->setParameter('product_image_list_image_text_type', ImageTitle::TYPE)
                    ->setParameter('product_image_list_image_text_value', $title->text())
                    ->executeQuery();
            }
            
            foreach ($newImage->altTitles() as $altTitle) {
                $languageId = $this->getLanguageIdFromLanguageCode($altTitle->languageCode());
                
                $this->connection->createQueryBuilder()
                    ->insert('product_image_list_image_text')
                    ->setValue('language_id', ':language_id')
                    ->setValue('product_image_list_image_id', ':product_image_list_image_id')
                    ->setValue('product_image_list_image_text_type', ':product_image_list_image_text_type')
                    ->setValue('product_image_list_image_text_value', ':product_image_list_image_text_value')
                    ->setParameter('language_id', $languageId)
                    ->setParameter('product_image_list_image_id', $imageId)
                    ->setParameter('product_image_list_image_text_type', ImageAltTitle::TYPE)
                    ->setParameter('product_image_list_image_text_value', $altTitle->text())
                    ->executeQuery();
            }
        }
    }
    
    
    /**
     * @param int   $id
     * @param Image $removedImage
     *
     * @return int
     * @throws ImageDoesNotExistException
     * @throws \Doctrine\DBAL\Exception
     */
    private function getImageIdByRelativePathAndListId(int $id, Image $removedImage): int
    {
        $relativePath = $removedImage->relativePath();
        
        $result = $this->connection->createQueryBuilder()
            ->select('`pili`.`product_image_list_image_id`')
            ->from('product_image_list', 'pil')
            ->leftJoin('pil',
                       'product_image_list_image',
                       'pili',
                       '`pil`.`product_image_list_id`=`pili`.`product_image_list_id`')
            ->leftJoin('pili',
                       'product_image_list_image_text',
                       'pilit',
                       '`pili`.`product_image_list_image_id`=`pilit`.`product_image_list_image_id`')
            ->groupBy('`pili`.`product_image_list_image_id`')
            ->where('`pil`.`product_image_list_id` = :product_image_list_id')
            ->setParameter('product_image_list_id', $id)
            ->where('`pili`.`product_image_list_image_local_path` = :product_image_list_image_local_path')
            ->setParameter('product_image_list_image_local_path', $relativePath)
            ->executeQuery();
        
        if ($result->rowCount() === 0) {
            throw ImageDoesNotExistException::forImage($removedImage);
        }
        
        return (int)$result->fetchAllAssociative()[0]['product_image_list_image_id'];
    }
    
    
    /**
     * @param string $code
     *
     * @return int
     * @throws \Doctrine\DBAL\Exception
     */
    private function getLanguageIdFromLanguageCode(string $code): int
    {
        if (isset($this->languageCodeToIdMap[$code]) === true) {
            return $this->languageCodeToIdMap[$code];
        }
        
        return $this->languageCodeToIdMap[$code] = (int)$this->connection->createQueryBuilder()
                                                            ->select('languages_id')
                                                            ->from('languages')
                                                            ->where('code = :code')
                                                            ->setParameter('code', $code)
                                                            ->executeQuery()
                                                            ->fetchAllAssociative()[0]['languages_id'];
    }
    
    
    /**
     * Determines if an image is not used in another product or list
     *
     * @param string $relativePath
     *
     * @return string|null returns relativePath if it is no longer used and null if it is
     * @throws \Doctrine\DBAL\Exception
     */
    private function imageIsNotUsedInProductsOrLists(string $relativePath): ?string
    {
        $query = "
            SELECT
            (SELECT COUNT(*) FROM `product_image_list_image` WHERE `product_image_list_image_local_path` = :path) AS 'usage_in_lists',
            (SELECT COUNT(*) FROM `products` WHERE `products_image` = :path) AS 'usage_as_product_main_image',
            (SELECT COUNT(*) FROM `products_images` WHERE `image_name` = :path) AS 'usage_as_product_image'";
        
        $stmt = $this->connection->prepare($query);
        $stmt->bindValue(':path', $relativePath);
        
        $usage = $stmt->executeQuery()->fetchNumeric();
        $usage = array_map('intval', $usage);
        $usage = array_sum($usage);
        
        return $usage === 0 ? $this->imagesDirectory->value() . DIRECTORY_SEPARATOR . $relativePath : null;
    }
    
    
    /**
     * Removes an image from the filesystem
     *
     * @param string $absolutePath
     *
     * @return void
     */
    private function deleteImageFromFilesystem(string $absolutePath): void
    {
        unlink($absolutePath);
    }
}