<?php
/*--------------------------------------------------------------
   ImageListMapper.php 2021-09-08
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ImageList\App\Data;

use Gambio\Admin\Modules\ImageList\Model\Collections\ImageAltTitles;
use Gambio\Admin\Modules\ImageList\Model\Collections\ImageListIds;
use Gambio\Admin\Modules\ImageList\Model\Collections\ImageLists;
use Gambio\Admin\Modules\ImageList\Model\Collections\Images;
use Gambio\Admin\Modules\ImageList\Model\Collections\ImageTitles;
use Gambio\Admin\Modules\ImageList\Model\Entities\Image;
use Gambio\Admin\Modules\ImageList\Model\ImageList;
use Gambio\Admin\Modules\ImageList\Model\ValueObjects\ImageAltTitle;
use Gambio\Admin\Modules\ImageList\Model\ValueObjects\ImageListId;
use Gambio\Admin\Modules\ImageList\Model\ValueObjects\ImagePath;
use Gambio\Admin\Modules\ImageList\Model\ValueObjects\ImageTitle;
use Gambio\Admin\Modules\ImageList\Model\ValueObjects\ImageUrl;
use Gambio\Admin\Modules\ImageList\Model\ValueObjects\NewImage;
use Gambio\Admin\Modules\ImageList\Services\ImageListFactory;

/**
 * Class ImageListMapper
 * @package Gambio\Admin\Modules\ImageList\App\Data
 */
class ImageListMapper
{
    /**
     * @var ImageListFactory
     */
    private $factory;
    
    
    /**
     * ImageListMapper constructor.
     *
     * @param ImageListFactory $factory
     */
    public function __construct(ImageListFactory $factory)
    {
        $this->factory = $factory;
    }
    
    
    /**
     * @param array $imageLists
     *
     * @return ImageLists
     */
    public function mapImageLists(array $imageLists): ImageLists
    {
        $lists = array_map([$this, 'mapImageList'], $imageLists);
        
        return ImageLists::create(...$lists);
    }
    
    
    /**
     * @param array $imageList
     *
     * @return ImageList
     */
    public function mapImageList(array $imageList): ImageList
    {
        $listId    = $this->factory->createImageListId((int)$imageList['product_image_list_id']);
        $listName  = $this->factory->createImageListName($imageList['product_image_list_name']);
        $images    = $this->mapImages($imageList['product_image_list_images']);
        $newImages = $this->factory->createNewImages();
        
        return ImageList::create($listId, $listName, $images, $newImages);
    }
    
    
    /**
     * @param array $images
     *
     * @return Images
     */
    private function mapImages(array $images): Images
    {
        $images = array_map([$this, 'mapImage'], $images);
        
        return $this->factory->createImages(...$images);
    }
    
    
    /**
     * @param array $image
     *
     * @return Image
     */
    private function mapImage(array $image): Image
    {
        $localPath = $this->factory->createImagePath($image['product_image_list_image_local_path']);
        $webPath   = $this->factory->createImageWebPath($image['product_image_list_image_local_path']);
        $sortOrder = (int)$image['product_image_list_image_sort_order'];
        $titles    = $altTitles = [];
        
        if (isset($image['product_image_list_image_texts']['title'])) {
            
            foreach ($image['product_image_list_image_texts']['title'] as $languageCode => $title) {
                
                $titles[] = $this->factory->createImageTitle($languageCode, $title);
            }
        }
        
        if (isset($image['product_image_list_image_texts']['alt_title'])) {
            
            foreach ($image['product_image_list_image_texts']['alt_title'] as $languageCode => $title) {
                
                $altTitles[] = $this->factory->createImageAltTitle($languageCode, $title);
            }
        }
        
        $titles    = $this->factory->createImageTitles(...$titles);
        $altTitles = $this->factory->createImageAltTitles(...$altTitles);
        
        return $this->factory->createImage($localPath, $webPath, $titles, $altTitles, $sortOrder);
    }
    
    
    /**
     * @param int $imageListId
     *
     * @return ImageListId
     */
    public function mapImageListId(int $imageListId): ImageListId
    {
        return $this->factory->createImageListId($imageListId);
    }
    
    
    /**
     * @param int[] $imageListId
     *
     * @return ImageListIds
     */
    public function mapImageListIds(array $imageListId): ImageListIds
    {
        $ids = array_map([$this, 'mapImageListId'], $imageListId);
        
        return $this->factory->createImageListIds(...$ids);
    }
    
    /**
     * @param string $languageCode
     * @param string $text
     *
     * @return ImageTitle
     */
    public function mapImageTitle(string $languageCode, string $text): ImageTitle
    {
        return $this->factory->createImageTitle($languageCode, $text);
    }
    
    
    /**
     * @param string $languageCode
     * @param string $text
     *
     * @return ImageAltTitle
     */
    public function mapImageAltTitle(string $languageCode, string $text): ImageAltTitle
    {
        return $this->factory->createImageAltTitle($languageCode, $text);
    }
    
    
    /**
     * @param ImageTitle ...$imageTitles
     *
     * @return ImageTitles
     */
    public function mapImageTitles(ImageTitle ...$imageTitles): ImageTitles
    {
        return $this->factory->createImageTitles(...$imageTitles);
    }
    
    
    /**
     * @param ImageAltTitle ...$imageTitles
     *
     * @return ImageAltTitles
     */
    public function mapImageAltTitles(ImageAltTitle ...$imageTitles): ImageAltTitles
    {
        return $this->factory->createImageAltTitles(...$imageTitles);
    }
    
    /**
     * @param ImagePath      $localPath
     * @param ImageTitles    $titles
     * @param ImageAltTitles $altTitles
     * @param int            $sortOrder
     *
     * @return NewImage
     */
    public function mapNewImage(
        ImagePath $localPath,
        ImageTitles $titles,
        ImageAltTitles $altTitles,
        int $sortOrder
    ): NewImage {
        
        return $this->factory->createNewImage($localPath, $titles, $altTitles, $sortOrder);
    }
}