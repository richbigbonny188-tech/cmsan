<?php
/*--------------------------------------------------------------
   RecalculateProductVariantsPricesOnOptionValueUpdatedEventListener.php 2023-06-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\Variant\App\EventListeners;

use Gambio\Admin\Modules\Option\Model\Events\OptionValueUpdated;
use Gambio\Admin\Modules\Product\Submodules\Variant\Services\RecalculateProductVariantPriceService;
use Gambio\Core\Event\PrioritizedEventListener;

/**
 * Class RecalculateProductVariantPriceOnOptionValueUpdatedEventLister
 *
 * @package Gambio\Admin\Modules\Product\Submodules\Variant\App\EventListeners
 */
class RecalculateProductVariantsPricesOnOptionValueUpdatedEventListener implements PrioritizedEventListener
{
    /**
     * RecalculateProductVariantsPricesOnOptionValueUpdatedEventListener constructor.
     */
    public function __construct(private RecalculateProductVariantPriceService $recalculateService) { }
    
    
    /**
     * @param OptionValueUpdated $event
     */
    public function __invoke(OptionValueUpdated $event): void
    {
        $this->recalculateService->recalculateForVariantsWithOptionValue($event->id()->value(),
                                                                         $event->optionValue()->id());
    }
    
    
    /**
     * @inheritDoc
     */
    public function priority(): int
    {
        return self::PRIORITY_VERY_HIGH;
    }
}