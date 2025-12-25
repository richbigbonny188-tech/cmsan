<?php
/**
 * ServiceProvider.php 2020-10-19
 * Gambio GmbH
 * http://www.gambio.de
 * Copyright (c) 2020 Gambio GmbH
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
 */

declare(strict_types=1);

namespace Gambio\Shop\SellingUnit\Presentation;

use Gambio\Core\Application\DependencyInjection\AbstractBootableServiceProvider;
use Gambio\Shop\SellingUnit\Presentation\ValueObjects\OutOfStockMarkings;

/**
 * Class ServiceProvider
 *
 * @package Gambio\Shop\SellingUnit\Presentation
 */
class ServiceProvider extends AbstractBootableServiceProvider
{
    public function provides(): array
    {
        return [
            OutOfStockMarkings::class
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->application->registerShared(OutOfStockMarkings::class)->addArgument(STOCK_MARK_PRODUCT_OUT_OF_STOCK);
    }
    
    
    /**
     * @inheritDoc
     */
    public function boot(): void
    {
    
    }
}