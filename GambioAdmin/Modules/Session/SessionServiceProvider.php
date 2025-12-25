<?php
/* --------------------------------------------------------------
 SessionServiceProvider.php 2021-08-31
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2021 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Modules\Session;

use Gambio\Admin\Modules\Session\App\Actions\SessionKeepAlive;
use Gambio\Core\Application\DependencyInjection\AbstractServiceProvider;
use Gambio\Core\Configuration\Services\ConfigurationService;

/**
 * Class SessionServiceProvider
 *
 * @package Gambio\Admin\Modules\Session
 */
class SessionServiceProvider extends AbstractServiceProvider
{
    /**
     * @inheritDoc
     */
    public function provides(): array
    {
        return [
            SessionKeepAlive::class,
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->application->registerShared(SessionKeepAlive::class)->addArgument(ConfigurationService::class);
    }
}