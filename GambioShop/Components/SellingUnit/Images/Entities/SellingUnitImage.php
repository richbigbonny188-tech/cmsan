<?php
/*--------------------------------------------------------------------
 SellingUnitImage.php 2022-04-15
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2022 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

namespace Gambio\Shop\SellingUnit\Images\Entities;

use Gambio\Shop\SellingUnit\Images\Entities\Interfaces\SellingUnitImageInterface;
use Gambio\Shop\SellingUnit\Images\ValueObjects\AbstractImageSource;
use Gambio\Shop\SellingUnit\Images\ValueObjects\ImageAlternateText;
use Gambio\Shop\SellingUnit\Images\ValueObjects\ImageNumber;
use Gambio\Shop\SellingUnit\Images\ValueObjects\ImagePath;
use Gambio\Shop\SellingUnit\Images\ValueObjects\ImageUrl;
use Gambio\Shop\SellingUnit\Images\ValueObjects\ProductImageSource;

/**
 * Class SellingUnitImage
 * @package Gambio\Shop\SellingUnit\Images\Entities
 */
class SellingUnitImage implements SellingUnitImageInterface
{
    /**
     * @var ImageAlternateText
     */
    protected $alternateText;
    
    /**
     * @var ImageUrl
     */
    protected $infoUrl;
    
    /**
     * @var ImageNumber
     */
    protected $number;
    
    /**
     * @var ImagePath
     */
    protected $path;
    
    /**
     * @var ImageUrl
     */
    protected $url;
    
    /**
     * @var ImageUrl
     */
    protected $popUpUrl;
    
    /**
     * @var ImageUrl
     */
    protected $thumbnailUrl;
    
    /**
     * @var AbstractImageSource|null
     */
    protected $source;
    /**
     * @var ImageUrl
     */
    private $galleryUrl;


    /**
     * SellingUnitImage constructor.
     *
     * @param ImageUrl $url
     * @param ImagePath $path
     * @param ImageAlternateText $alternateText
     * @param ImageNumber $number
     * @param ImageUrl $infoUrl
     * @param ImageUrl $popUpUrl
     * @param ImageUrl $thumbnailUrl
     * @param ImageUrl $galleryUrl
     * @param AbstractImageSource|null $source
     */
    public function __construct(
        ImageUrl $url,
        ImagePath $path,
        ImageAlternateText $alternateText,
        ImageNumber $number,
        ImageUrl $infoUrl,
        ImageUrl $popUpUrl,
        ImageUrl $thumbnailUrl,
        ImageUrl $galleryUrl,
        ?AbstractImageSource $source = null
    ) {
        $this->url           = $url;
        $this->path          = $path;
        $this->alternateText = $alternateText;
        $this->number        = $number;
        $this->infoUrl       = $infoUrl;
        $this->popUpUrl      = $popUpUrl;
        $this->thumbnailUrl  = $thumbnailUrl;
        $this->source        = $source ?? new ProductImageSource;
        $this->galleryUrl    = $galleryUrl;
    }
    
    
    /**
     * @inheritDoc
     */
    public function url(): ImageUrl
    {
        return $this->url;
    }
    
    
    /**
     * @inheritDoc
     */
    public function popUpUrl(): ImageUrl
    {
        if (file_exists(__DIR__ . '/../../../../../' . $this->path()->value())) {
            return $this->url;
        } else {
            return $this->popUpUrl;
        }
    }
    
    
    /**
     * @inheritDoc
     */
    public function path(): ImagePath
    {
        return $this->path;
    }
    
    
    /**
     * @inheritDoc
     */
    public function infoUrl(): ImageUrl
    {
        return $this->infoUrl;
    }
    
    
    /**
     * @inheritDoc
     */
    public function alternateText(): ImageAlternateText
    {
        return $this->alternateText;
    }
    
    
    /**
     * @inheritDoc
     */
    public function number(): ImageNumber
    {
        return $this->number;
    }
    
    
    /**
     * @return ImageUrl
     */
    public function thumbNail(): ImageUrl
    {
        return $this->thumbnailUrl;
    }
    
    
    /**
     * @inheritDoc
     */
    public function source(): AbstractImageSource
    {
        return $this->source;
    }

    public function gallery(): ImageUrl
    {
        return $this->galleryUrl;
    }
}