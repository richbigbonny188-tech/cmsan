<?php
/* --------------------------------------------------------------
   ClearCacheServiceProvider.php 2023-09-04
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Cache;

use Gambio\Admin\Modules\Cache\App\Actions\ClearCacheOverview;
use Gambio\Core\Application\DependencyInjection\AbstractServiceProvider;

/**
 * Class ConfigurationServiceProvider
 *
 * @package Gambio\Admin\Modules\Configuration
 */
class ClearCacheServiceProvider extends AbstractServiceProvider
{
    /**
     * @inheritDoc
     */
    public function provides(): array
    {
        return [
            ClearCacheOverview::class
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->application->registerShared(ClearCacheOverview::class);
    }
}