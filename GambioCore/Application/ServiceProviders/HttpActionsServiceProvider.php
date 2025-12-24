<?php
/* --------------------------------------------------------------
 ModuleServiceProvider.php 2021-05-14
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Core\Application\ServiceProviders;

use Gambio\Core\Application\DependencyInjection\AbstractBootableServiceProvider;
use Gambio\Core\Application\Http\AbstractAction;
use Gambio\Core\Application\ValueObjects\Url;
use Gambio\Core\TextManager\Services\TextManager;

/**
 * Class HttpActionsServiceProvider
 *
 * @package Gambio\Core\Application\ServiceProviders
 */
class HttpActionsServiceProvider extends AbstractBootableServiceProvider
{
    /**
     * @inheritDoc
     */
    public function provides(): array
    {
        return [];
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
    }
    
    
    /**
     * @inheritDoc
     */
    public function boot(): void
    {
        $this->application->inflect(AbstractAction::class)
            ->invokeMethod('initAbstractAction', [Url::class, TextManager::class]);
    }
}