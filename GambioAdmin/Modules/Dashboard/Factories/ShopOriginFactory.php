<?php
/*--------------------------------------------------------------
   ShopOriginFactory.php 2020-10-23
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Dashboard\Factories;

use Gambio\Admin\Modules\Dashboard\ValueObjects\ShopOrigin;

/**
 * Class ShopOriginFactory
 * @package Gambio\Admin\Modules\Dashboard\Factories
 */
class ShopOriginFactory
{
    /**
     * @return ShopOrigin
     */
    public function createShopOrigin(): ShopOrigin
    {
        if ($this->isCloudShop()) {
            
            return new ShopOrigin(ShopOrigin::KEY_CLOUD);
        }
        
        if ($this->isFreeShop()) {
            
            return new ShopOrigin(ShopOrigin::KEY_FREE);
        }
        
        return new ShopOrigin(ShopOrigin::KEY_ON_PREMISE);
    }
    
    
    /**
     * @return bool
     */
    protected function isCloudShop(): bool
    {
        return class_exists(\GambioCloudPlan::class);
    }
    
    
    /**
     * @return bool
     */
    protected function isFreeShop(): bool
    {
        $versionInfoFilePath = dirname(__DIR__, 4);
        $versionInfoFilePath .= DIRECTORY_SEPARATOR . 'version_info' . DIRECTORY_SEPARATOR;
        $versionInfoFilePath .= 'free_origin';
        
        return file_exists($versionInfoFilePath);
    }
}