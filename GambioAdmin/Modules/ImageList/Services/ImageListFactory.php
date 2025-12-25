<?php
/*--------------------------------------------------------------
   ImageListFactory.php 2021-09-08
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ImageList\Services;

use Gambio\Admin\Modules\ImageList\Model\Collections\ImageAltTitles;
use Gambio\Admin\Modules\ImageList\Model\Collections\ImageListIds;
use Gambio\Admin\Modules\ImageList\Model\Collections\Images;
use Gambio\Admin\Modules\ImageList\Model\Collections\ImageTitles;
use Gambio\Admin\Modules\ImageList\Model\Collections\NewImages;
use Gambio\Admin\Modules\ImageList\Model\Entities\Image;
use Gambio\Admin\Modules\ImageList\Model\ValueObjects\ImageAltTitle;
use Gambio\Admin\Modules\ImageList\Model\ValueObjects\ImageListId;
use Gambio\Admin\Modules\ImageList\Model\ValueObjects\ImageListName;
use Gambio\Admin\Modules\ImageList\Model\ValueObjects\ImagePath;
use Gambio\Admin\Modules\ImageList\Model\ValueObjects\ImageTitle;
use Gambio\Admin\Modules\ImageList\Model\ValueObjects\ImageUrl;
use Gambio\Admin\Modules\ImageList\Model\ValueObjects\NewImage;
use Gambio\Core\Application\ValueObjects\Path;
use Gambio\Core\Application\ValueObjects\Url;

/**
 * Class ImageListFactory
 *
 * @package Gambio\Admin\Modules\ImageList\Services
 */
class ImageListFactory
{
    /**
     * @var Path
     */
    private $path;
    
    /**
     * @var Url
     */
    private $url;
    
    
    /**
     * ImageListFactory constructor.
     *
     * @param Path $path
     * @param Url  $url
     */
    public function __construct(Path $path, Url $url)
    {
        $this->path = $path;
        $this->url  = $url;
    }
    
    
    /**
     * @param int $id
     *
     * @return ImageListId
     */
    public function createImageListId(int $id): ImageListId
    {
        return ImageListId::create($id);
    }
    
    
    /**
     * @param ImageListId ...$imageListIds
     *
     * @return ImageListIds
     */
    public function createImageListIds(ImageListId ...$imageListIds): ImageListIds
    {
        return ImageListIds::create(...$imageListIds);
    }
    
    
    /**
     * @param string $imageListName
     *
     * @return ImageListName
     */
    public function createImageListName(string $imageListName): ImageListName
    {
        return ImageListName::create($imageListName);
    }
    
    
    /**
     * @param ImagePath      $localPath
     * @param ImageUrl       $webPath
     * @param ImageTitles    $titles
     * @param ImageAltTitles $altTitles
     * @param int            $sortOrder
     *
     * @return Image
     */
    public function createImage(
        ImagePath      $localPath,
        ImageUrl       $webPath,
        ImageTitles    $titles,
        ImageAltTitles $altTitles,
        int            $sortOrder
    ): Image {
        
        return Image::create($localPath,
                             $webPath,
                             $titles,
                             $altTitles,
                             $sortOrder);
    }
    
    
    /**
     * @param Image ...$images
     *
     * @return Images
     */
    public function createImages(Image ...$images): Images
    {
        return Images::create(...$images);
    }
    
    
    /**
     * @param ImagePath      $localPath
     * @param ImageTitles    $titles
     * @param ImageAltTitles $altTitles
     * @param int            $sortOrder
     *
     * @return NewImage
     */
    public function createNewImage(
        ImagePath      $localPath,
        ImageTitles    $titles,
        ImageAltTitles $altTitles,
        int            $sortOrder
    ): NewImage {
        
        return NewImage::create($localPath,
                                $titles,
                                $altTitles,
                                $sortOrder);
    }
    
    
    /**
     * @param NewImage ...$newImages
     *
     * @return NewImages
     */
    public function createNewImages(NewImage ...$newImages): NewImages
    {
        return NewImages::create(...$newImages);
    }
    
    
    /**
     * @param string $relativePath
     *
     * @return ImagePath
     */
    public function createImagePath(string $relativePath): ImagePath
    {
        return ImagePath::create($relativePath);
    }
    
    
    /**
     * @param string $relativePath
     *
     * @return ImageUrl
     */
    public function createImageWebPath(string $relativePath): ImageUrl
    {
        $webPath = $this->url->base() . '/images/product_images/original_images/'
                   . $this->urlEncodeRelativePath($relativePath);
        
        return ImageUrl::create($webPath);
    }
    
    
    /**
     * @param string $languageCode
     * @param string $text
     *
     * @return ImageTitle
     */
    public function createImageTitle(string $languageCode, string $text): ImageTitle
    {
        return ImageTitle::create($languageCode, $text);
    }
    
    
    /**
     * @param string $languageCode
     * @param string $text
     *
     * @return ImageAltTitle
     */
    public function createImageAltTitle(string $languageCode, string $text): ImageAltTitle
    {
        return ImageAltTitle::create($languageCode, $text);
    }
    
    
    /**
     * @param ImageTitle ...$imageTitles
     *
     * @return ImageTitles
     */
    public function createImageTitles(ImageTitle ...$imageTitles): ImageTitles
    {
        return ImageTitles::create(...$imageTitles);
    }
    
    
    /**
     * @param ImageAltTitle ...$imageAltTitles
     *
     * @return ImageAltTitles
     */
    public function createImageAltTitles(ImageAltTitle ...$imageAltTitles): ImageAltTitles
    {
        return ImageAltTitles::create(...$imageAltTitles);
    }
    
    
    /**
     * @param string $relativePath
     *
     * @return string
     */
    private function urlEncodeRelativePath(string $relativePath): string
    {
        if (strpos($relativePath, '/') !== false) {
            
            $relativePath = explode('/', $relativePath);
            $relativePath = array_map('rawurlencode', $relativePath);
            
            return implode('/', $relativePath);
        }
        
        return rawurlencode($relativePath);
    }
}