<?php
/*------------------------------------------------------------------------------
 ExternalDataStorage.php 2020-09-16
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -----------------------------------------------------------------------------*/

namespace Gambio\Admin\Modules\Dashboard\App\Data;

interface DashboardConfigurationStorage
{
    public function enableExternalSocialMediaEmbeds(): void;
    
    
    public function disableExternalSocialMediaEmbeds(): void;
    
    
    public function isExternalSocialMediaEmbedsAllowed(): bool;
}