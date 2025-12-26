<?php
/*--------------------------------------------------------------------
 SellingUnitImageInterface.php 2020-06-02
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

namespace Gambio\Shop\SellingUnit\Images\Entities\Interfaces;

use Gambio\Shop\SellingUnit\Images\ValueObjects\AbstractImageSource;
use Gambio\Shop\SellingUnit\Images\ValueObjects\ImageAlternateText;
use Gambio\Shop\SellingUnit\Images\ValueObjects\ImageNumber;
use Gambio\Shop\SellingUnit\Images\ValueObjects\ImagePath;
use Gambio\Shop\SellingUnit\Images\ValueObjects\ImageUrl;

/**
 * Interface SellingUnitImageInterface
 * @package Gambio\Shop\SellingUnit\Images\Entities\Interfaces
 */
interface SellingUnitImageInterface
{
    /**
     * @return ImageUrl
     */
    public function url(): ImageUrl;
    
    
    /**
     * @return ImageUrl
     */
    public function popUpUrl(): ImageUrl;
    
    
    /**
     * @return ImageUrl
     */
    public function infoUrl(): ImageUrl;
    
    
    /**
     * @return ImagePath
     */
    public function path(): ImagePath;
    
    
    /**
     * @return ImageAlternateText
     */
    public function alternateText(): ImageAlternateText;
    
    
    /**
     * @return ImageNumber
     */
    public function number(): ImageNumber;
    
    
    /**
     * @return ImageUrl
     */
    public function thumbNail(): ImageUrl;

    /**
     * @return ImageUrl
     */
    public function gallery(): ImageUrl;

    
    /**
     * @return AbstractImageSource
     */
    public function source(): AbstractImageSource;
}