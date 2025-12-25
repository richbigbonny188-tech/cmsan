<?php
/*--------------------------------------------------------------
   RebuildProductsPropertiesIndexOnUpdatedOptionDetail.php 2023-06-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\Variant\App\EventListeners;

use Gambio\Admin\Modules\Option\Model\Events\OptionDetailsUpdated;
use Gambio\Admin\Modules\Product\Submodules\Variant\App\Data\ProductVariantsReader;
use Gambio\Admin\Modules\Product\Submodules\Variant\App\Data\ProductVariantsUpdater;
use Gambio\Core\Event\PrioritizedEventListener;

/**
 * Class RebuildProductsPropertiesIndexOnUpdatedOptionDetail
 *
 * @package Gambio\Admin\Modules\Product\Submodules\Variant\App\EventListeners
 */
class RebuildProductsPropertiesIndexOnUpdatedOptionDetail implements PrioritizedEventListener
{
    /**
     * RebuildProductsPropertiesIndexOnUpdatedOptionDetail constructor.
     *
     * @param ProductVariantsReader  $reader
     * @param ProductVariantsUpdater $updater
     */
    public function __construct(
        private ProductVariantsReader  $reader,
        private ProductVariantsUpdater $updater
    ) {
    }
    
    
    /**
     * @param OptionDetailsUpdated $event
     */
    public function __invoke(OptionDetailsUpdated $event): void
    {
        $variantIds = $this->reader->variantsContainingOptions($event->id()->value());
        $this->updater->indexCombiAndOptions(...$variantIds);
    }
    
    
    /**
     * @inheritDoc
     */
    public function priority(): int
    {
        return self::PRIORITY_NORMAL;
    }
}