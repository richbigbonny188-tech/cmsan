<?php
/*------------------------------------------------------------------------------
 ActionUpdateSocialMediaEmbeds.php 2020-10-19
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -----------------------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Dashboard\App\Actions;

use Gambio\Admin\Modules\Dashboard\App\Data\DashboardConfigurationStorage;
use Gambio\Core\Application\Http\AbstractAction;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response as HttpResponse;

/**
 * Class ActionUpdateSocialMediaEmbeds
 *
 * @package Gambio\Admin\Modules\Dashboard\App\Actions
 * @codeCoverageIgnore
 */
class ActionUpdateSocialMediaEmbeds extends AbstractAction
{
    
    /**
     * @var DashboardConfigurationStorage
     */
    private $storage;
    
    
    public function handle(Request $request, HttpResponse $response): HttpResponse
    {
        if ($request->getAttribute('status') === 'true') {
            $this->storage->enableExternalSocialMediaEmbeds();
        } else {
            $this->storage->disableExternalSocialMediaEmbeds();
        }
        
        return $response;
    }
    
    
    /**
     * ActionUpdateSocialMediaEmbeds constructor.
     *
     * @param DashboardConfigurationStorage $storage
     */
    public function __construct(
        DashboardConfigurationStorage $storage
    ) {
        
        $this->storage = $storage;
    }
}