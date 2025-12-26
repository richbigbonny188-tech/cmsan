<?php
/* --------------------------------------------------------------
   LoadShopInformation.php 2023-11-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\Application\Bootstrapper;

use Gambio\Core\Application\Application;
use Gambio\Core\Application\Bootstrapper;
use Gambio\Core\Application\ValueObjects\ShopInformation;

/**
 * Class LoadShopInformation
 *
 * @package Gambio\Core\Application\Bootstrapper
 */
class LoadShopInformation implements Bootstrapper
{
    
    /**
     * @param Application $application
     *
     * @return void
     */
    public function boot(Application $application): void
    {
        $isCloud     = file_exists(__DIR__ . '/../../../version_info/cloud.php');
        $hasContract = file_exists(__DIR__ . '/../../../debug/.contract');
        
        $application->registerShared(ShopInformation::class)->addArgument($isCloud)->addArgument($hasContract);
    }
}