<?php
/*--------------------------------------------------------------------
 ServiceProvider.php 2020-2-19
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\SellingUnit\Unit;

use Gambio\Core\Application\DependencyInjection\AbstractBootableServiceProvider;
use Gambio\Shop\SellingUnit\Unit\Factories\Interfaces\SellingUnitIdFactoryInterface;
use Gambio\Shop\SellingUnit\Unit\Factories\SellingUnitIdFactory;

/**
 * Class ServiceProvider
 *
 * @package Gambio\Shop\SellingUnit\Unit
 */
class ServiceProvider extends AbstractBootableServiceProvider
{
    /**
     * @var string[]
     */
    public function provides(): array
    {
        return [
            SellingUnitIdFactoryInterface::class
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->application->registerShared(SellingUnitIdFactoryInterface::class, SellingUnitIdFactory::class);
    }
    
    
    /**
     * @inheritDoc
     */
    public function boot(): void
    {
    
    }
}