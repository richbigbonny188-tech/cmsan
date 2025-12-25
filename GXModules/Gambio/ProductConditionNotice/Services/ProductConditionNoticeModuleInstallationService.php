<?php
/* --------------------------------------------------------------
   ProductConditionNoticeModuleInstallationService.php 2021-12-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace GXModules\Gambio\ProductConditionNotice\Services;

/**
 * Interface ProductConditionNoticeModuleInstallationService
 *
 * @package GXModules\Gambio\ProductConditionNotice\Services
 */
interface ProductConditionNoticeModuleInstallationService
{
    /**
     * Installs the product condition notice module.
     */
    public function installModule(): void;
    
    
    /**
     * Uninstalls the product condition notice module.
     */
    public function uninstallModule(): void;
}