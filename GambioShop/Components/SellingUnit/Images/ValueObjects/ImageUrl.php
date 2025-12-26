<?php
/*--------------------------------------------------------------------
 ImageUrl.php 2020-2-12
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\SellingUnit\Images\ValueObjects;

/**
 * Class ImageUrl
 * @package Gambio\Shop\SellingUnit\ProductInformation\Services\ProductImage\ValueObjects
 */
class ImageUrl
{
    /**
     * @var string
     */
    protected $url;
    
    
    /**
     * ImageUrl constructor.
     *
     * @param string $url
     */
    public function __construct(string $url)
    {
        $this->url = $url;
    }
    
    
    /**
     * @return string
     */
    public function value(): string
    {
        return $this->url;
    }
}