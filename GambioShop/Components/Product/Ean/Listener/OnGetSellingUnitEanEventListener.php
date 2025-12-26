<?php
/*------------------------------------------------------------------------------
 OnGetSellingUnitEanEventListener.php 2021-06-02
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2021 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -----------------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\Product\Ean\Listener;

use Gambio\Core\Event\Abstracts\AbstractPrioritizedEventListener;
use Gambio\Shop\SellingUnit\Database\Unit\Events\Interfaces\OnGetSellingUnitEanEventInterface;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\Ean;

/**
 * Class OnGetProductEanEventListener
 * @package Gambio\Shop\Product\Ean\Listener
 */
class OnGetSellingUnitEanEventListener extends AbstractPrioritizedEventListener
{
    public const PRIORITY = self::PRIORITY_LOW;
    
    
    /**
     * @inheritDoc
     */
    public function priority(): int
    {
        return self::PRIORITY_LOW;
    }
    
    
    /**
     * @param OnGetSellingUnitEanEventInterface $event
     */
    public function __invoke(OnGetSellingUnitEanEventInterface $event)
    {
        $productEan = new Ean($event->product()->getEan());
        $event->builder()->withEanAtPos($productEan, 1000);
    }
    
}