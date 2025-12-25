<?php
/* --------------------------------------------------------------
   UserConfigurationServiceProvider.php 2021-05-21
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\UserConfiguration;

use Gambio\Admin\Modules\UserConfiguration\App\UserConfigurationController;
use Gambio\Core\Application\DependencyInjection\AbstractServiceProvider;
use Gambio\Core\UserConfiguration\Services\CurrentUserConfigurationService;

/**
 * Class UserConfigurationServiceProvider
 *
 * @package Gambio\Core\ServiceProvider
 */
class UserConfigurationServiceProvider extends AbstractServiceProvider
{
    /**
     * @inheritDoc
     */
    public function provides(): array
    {
        return [
            UserConfigurationController::class,
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->application->registerShared(UserConfigurationController::class)
            ->addArgument(CurrentUserConfigurationService::class);
    }
}