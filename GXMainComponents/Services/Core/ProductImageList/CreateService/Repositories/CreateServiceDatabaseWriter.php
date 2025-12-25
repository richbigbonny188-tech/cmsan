<?php
/* --------------------------------------------------------------
  CreateServiceDatabaseWriter.php 2023-03-06
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2023 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\ProductImageList\CreateService\Repositories;

use CI_DB_query_builder;
use Gambio\ProductImageList\CreateService\Interfaces\CreateServiceDatabaseWriterInterface;
use Gambio\ProductImageList\CreateService\Interfaces\ImageListImageDtoInterface;
use Gambio\ProductImageList\Image\Collections\TextCollection;
use Gambio\ProductImageList\Image\ValueObjects\LocalFilePath;
use Gambio\ProductImageList\ImageList\ValueObjects\ListId;
use LanguageProviderInterface;

/**
 * Class CreateServiceDatabaseWriter
 * @package Gambio\ProductImageList\CreateService\Repositories
 */
class CreateServiceDatabaseWriter implements CreateServiceDatabaseWriterInterface
{
    protected const IMAGE_LIST_TABLE_NAME              = 'product_image_list';
    protected const IMAGE_LIST_NAME_COLUMN             = 'product_image_list_name';
    protected const IMAGE_LIST_IMAGE_TABLE_NAME        = 'product_image_list_image';
    protected const IMAGE_LIST_LIST_ID_COLUMN          = 'product_image_list_id';
    protected const IMAGE_LIST_IMAGE_LOCAL_PATH_COLUMN = 'product_image_list_image_local_path';
    protected const IMAGE_LIST_TEXT_TABLE_NAME         = 'product_image_list_image_text';
    protected const IMAGE_LIST_IMAGE_ID_COLUMN         = 'product_image_list_image_id';
    protected const IMAGE_LIST_IMAGE_SORT_ORDER_COLUMN = 'product_image_list_image_sort_order';
    protected const IMAGE_LIST_TEXT_TYPE_COLUMN        = 'product_image_list_image_text_type';
    protected const IMAGE_LIST_TEXT_VALUE_COLUMN       = 'product_image_list_image_text_value';
    protected const IMAGE_LIST_LANGUAGE_ID_COLUMN      = 'language_id';
    
    /**
     * @var CI_DB_query_builder
     */
    protected $queryBuilder;
    /**
     * @var LanguageProviderInterface
     */
    protected $languageProvider;
    
    
    /**
     * CreateServiceDatabaseWriter constructor.
     *
     * @param CI_DB_query_builder       $queryBuilder
     * @param LanguageProviderInterface $languageProvider
     */
    public function __construct(CI_DB_query_builder $queryBuilder, LanguageProviderInterface $languageProvider)
    {
        $this->queryBuilder     = $queryBuilder;
        $this->languageProvider = $languageProvider;
    }
    
    
    /**
     * @inheritDoc
     */
    public function createImageList(string $listName): void
    {
        $this->queryBuilder->insert(self::IMAGE_LIST_TABLE_NAME, [self::IMAGE_LIST_NAME_COLUMN => $listName]);
    }
    
    
    /**
     * @inheritDoc
     */
    public function createImage(ImageListImageDtoInterface $image): int
    {
        $listId    = $image->listId();
        $sortIndex = $this->getUpcomingSortingIndex($listId);
        
        $this->queryBuilder->insert(self::IMAGE_LIST_IMAGE_TABLE_NAME,
                                               [
                                                   self::IMAGE_LIST_LIST_ID_COLUMN          => $listId,
                                                   self::IMAGE_LIST_IMAGE_LOCAL_PATH_COLUMN => $image->localPath(),
                                                   self::IMAGE_LIST_IMAGE_SORT_ORDER_COLUMN => $sortIndex
                                               ]);
     
        $imageId = $this->queryBuilder->insert_id();
        
        return (int)$imageId;
    }
    
    
    /**
     * @param int $listId
     *
     * @return int
     */
    protected function getUpcomingSortingIndex(int $listId): int
    {
        return count($this->queryBuilder->select(self::IMAGE_LIST_IMAGE_SORT_ORDER_COLUMN)
                         ->from(self::IMAGE_LIST_IMAGE_TABLE_NAME)
                         ->where([self::IMAGE_LIST_LIST_ID_COLUMN => $listId,])
                         ->get()
                         ->result_array());
    }
    
    /**
     * @param TextCollection $textCollection
     */
    protected function createTextForImage(TextCollection $textCollection): void
    {
        if (count($textCollection)) {
            
            $insertData = [];
            
            foreach ($textCollection as $text) {
                
                $insertData[] = [
                    self::IMAGE_LIST_IMAGE_ID_COLUMN    => $text->imageId()->value(),
                    self::IMAGE_LIST_TEXT_TYPE_COLUMN   => $text->type(),
                    self::IMAGE_LIST_TEXT_VALUE_COLUMN  => $text->value(),
                    self::IMAGE_LIST_LANGUAGE_ID_COLUMN => $this->languageProvider->getIdByCode(new \LanguageCode(new \StringType($text->languageCode()
                                                                                                                                      ->value())))
                ];
            }
            
            $this->queryBuilder->insert_batch(self::IMAGE_LIST_TEXT_TABLE_NAME, $insertData);
        }
    }
    
    
    /**
     * @inheritDoc
     */
    public function createImageTexts(TextCollection $titles, TextCollection $altTitles): void
    {
        $this->createTextForImage($titles);
        $this->createTextForImage($altTitles);
    }
}