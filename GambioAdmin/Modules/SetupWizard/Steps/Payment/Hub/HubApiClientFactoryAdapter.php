<?php
/*------------------------------------------------------------------------------
 HubApiClientFactoryAdapter.php 2020-08-19
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -----------------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\SetupWizard\Steps\Payment\Hub;


/**
 * Class HubApiClientFactoryAdapter
 * @package Gambio\Admin\Modules\SetupWizard\Steps\Payment\Hub
 * @codeCoverageIgnore
 */
class HubApiClientFactoryAdapter implements HubApiClientFactory
{
    public function createHubSession()
    {
        return null;
    }
}