<?php
/*--------------------------------------------------------------------
 OnGetProductInfoEventListener.php 2020-2-24
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\Product\Weight\Listener;

use Gambio\Shop\SellingUnit\Database\Unit\Events\Interfaces\OnGetProductInfoEventInterface;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\ShowWeight;

/**
 * Class OnGetProductInfoEventListener
 * @package Gambio\Shop\Product\Weight\Listener
 */
class OnGetProductInfoEventListener
{
    /**
     * @param OnGetProductInfoEventInterface $event
     */
    public function __invoke(OnGetProductInfoEventInterface $event)
    {
        $event->builder()->withShowWeight(new ShowWeight($event->product()->showWeight()));
    }
}