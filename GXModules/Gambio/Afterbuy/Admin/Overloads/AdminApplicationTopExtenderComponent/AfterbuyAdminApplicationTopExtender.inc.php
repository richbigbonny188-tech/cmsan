<?php
/* --------------------------------------------------------------
   AfterbuyAdminApplicationTopExtender.php 2023-10-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

use GXModules\Gambio\Afterbuy\Admin\Module\AfterbuyServiceProvider;

/**
 * Class AfterbuyAdminApplicationTopExtender
 *
 * @package GXModules\Gambio\Afterbuy\Admin\Overloads\AdminApplicationTopExtenderComponent
 */
class AfterbuyAdminApplicationTopExtender extends AfterbuyAdminApplicationTopExtender_parent
{
    /**
     * Registers the `AfterbuyServiceProvider` for the `LegacyDependencyContainer`.
     *
     * @return void
     */
    public function proceed()
    {
        parent::proceed();
        
        LegacyDependencyContainer::getInstance()->registerProvider(AfterbuyServiceProvider::class);
    }
}