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

namespace Gambio\Shop\SellingUnit\Database\Image;

use Gambio\Core\Application\DependencyInjection\AbstractBootableServiceProvider;
use Gambio\Shop\SellingUnit\Images\Builders\CollectionBuilder;
use Gambio\Shop\SellingUnit\Images\ValueObjects\SelectedCollectionType;

/**
 * Class ServiceProvider
 *
 * @package Gambio\Shop\SellingUnit\Database\Image
 */
class ServiceProvider extends AbstractBootableServiceProvider
{
    /**
     * @var string[]
     */
    public function provides(): array
    {
        return [
            CollectionBuilder::class,
            SelectedCollectionType::class
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->application->registerShared(CollectionBuilder::class);
        $this->application->registerShared(SelectedCollectionType::class)->addArgument(CollectionBuilder::class);
    }
    
    
    /**
     * @inheritDoc
     */
    public function boot(): void
    {
    
    }
    
}