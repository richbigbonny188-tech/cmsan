<?php
/* --------------------------------------------------------------
   GambioHubAdminApplicationTopExtenderComponent.inc.php 2023-02-08
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);

use Gambio\Admin\Modules\TrackingCode\Model\Events\TrackingCodeCreated;
use GXModules\Gambio\Hub\Admin\Classes\Extensions\TrackingCodeCreatedListener;
use GXModules\Gambio\Hub\Admin\Module\HubServiceProvider;

class GambioHubAdminApplicationTopExtenderComponent extends GambioHubAdminApplicationTopExtenderComponent_parent
{
    public function proceed()
    {
        parent::proceed();
        LegacyDependencyContainer::getInstance()->registerProvider(HubServiceProvider::class);
        LegacyDependencyContainer::getInstance()
            ->attachEventListener(TrackingCodeCreated::class, TrackingCodeCreatedListener::class);
    }
}
