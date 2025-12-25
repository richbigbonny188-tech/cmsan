<?php
/* --------------------------------------------------------------
   LanguageSerivceProvider.php 2020-10-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\Language;

use Gambio\Admin\Modules\Language\LanguageServiceProvider as AdminLanguageServiceProvider;
use Gambio\Admin\Modules\Language\Services\LanguageFilterService;
use Gambio\Admin\Modules\Language\Services\LanguageReadService;
use Gambio\Core\Application\DependencyInjection\AbstractServiceProvider;

/**
 * Class LanguageServiceProvider
 *
 * @package Gambio\Core\Language
 */
class LanguageServiceProvider extends AbstractServiceProvider
{
    /**
     * @inheritDoc
     */
    public function provides(): array
    {
        return [
            Services\LanguageService::class,
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        if ($this->application->has(LanguageReadService::class) === false
            || $this->application->has(LanguageFilterService::class) === false) {
            $this->application->registerProvider(AdminLanguageServiceProvider::class);
        }
        
        $this->application->registerShared(Services\LanguageService::class, App\LanguageService::class)
            ->addArgument(LanguageReadService::class)
            ->addArgument(LanguageFilterService::class);
    }
}