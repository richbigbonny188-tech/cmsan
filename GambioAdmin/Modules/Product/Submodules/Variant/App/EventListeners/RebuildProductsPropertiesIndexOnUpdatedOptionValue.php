<?php
/*--------------------------------------------------------------
   RebuildProductsPropertiesIndexOnUpdatedOptionValue.php 2023-06-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\Variant\App\EventListeners;

use Gambio\Admin\Modules\Option\Model\Events\OptionValueUpdated;
use Gambio\Admin\Modules\Product\Submodules\Variant\App\Data\ProductVariantsReader;
use Gambio\Admin\Modules\Product\Submodules\Variant\App\Data\ProductVariantsUpdater;
use Gambio\Core\Event\PrioritizedEventListener;

/**
 * Class RebuildProductsPropertiesIndexOnUpdatedOptionValue
 *
 * @package Gambio\Admin\Modules\Product\Submodules\Variant\App\EventListeners
 */
class RebuildProductsPropertiesIndexOnUpdatedOptionValue implements PrioritizedEventListener
{
    /**
     * RebuildProductsPropertiesIndexOnUpdatedOptionValue constructor.
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
     * @param OptionValueUpdated $event
     */
    public function __invoke(OptionValueUpdated $event)
    {
        $variantIds = $this->reader->variantsContainingOptionValues($event->optionValue()->id());
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