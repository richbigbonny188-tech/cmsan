<?php
/*
 * --------------------------------------------------------------
 *   PriceServiceProvider.php 2020-10-14
 *   Gambio GmbH
 *   http://www.gambio.de
 *   Copyright (c) 2021 Gambio GmbH
 *   Released under the GNU General Public License (Version 2)
 *   [http://www.gnu.org/licenses/gpl-2.0.html]
 * --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Modules\Price;

use Doctrine\DBAL\Connection;
use Gambio\Admin\Modules\Price\App\Data\ProductPriceConversionReader;
use Gambio\Admin\Modules\Price\Services\ProductPriceConversionService;
use Gambio\Core\Application\DependencyInjection\AbstractServiceProvider;
use Gambio\Core\Configuration\Services\ConfigurationFinder;

/**
 * Class PriceServiceProvider
 *
 * @package Gambio\Admin\Modules\Price
 */
class PriceServiceProvider extends AbstractServiceProvider
{
    
    /**
     * @inheritDoc
     */
    public function provides(): array
    {
        return [
            ProductPriceConversionService::class,
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->application->register(ProductPriceConversionReader::class)
            ->addArgument(ConfigurationFinder::class)
            ->addArgument(Connection::class);
        
        $this->application->registerShared(ProductPriceConversionService::class,
                                           App\ProductPriceConversionService::class)
            ->addArgument(ProductPriceConversionReader::class);
    }
}