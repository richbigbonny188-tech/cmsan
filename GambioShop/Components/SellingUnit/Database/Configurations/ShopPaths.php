<?php
/*--------------------------------------------------------------------
 ShopPaths.php 2020-2-13
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\SellingUnit\Database\Configurations;

/**
 * Class ShopPaths
 * @package Gambio\Shop\SellingUnit\Database\Configurations
 */
class ShopPaths
{
    /**
     * @var string
     */
    protected $absolutePath;
    
    /**
     * @var string
     */
    protected $shopUrl;
    
    
    /**
     * ShopPaths constructor.
     *
     * @param string $absolutePath
     * @param string $shopUrl
     */
    public function __construct(string $absolutePath, string $shopUrl)
    {
        $this->absolutePath = $absolutePath;
        $this->shopUrl      = $shopUrl;
    }
    
    
    /**
     * @return string
     */
    public function absolutePath(): string
    {
        return $this->absolutePath;
    }
    
    
    /**
     * @return string
     */
    public function shopUrl(): string
    {
        return $this->shopUrl;
    }
}