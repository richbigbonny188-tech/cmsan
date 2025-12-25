<?php
/* --------------------------------------------------------------
  OnGetProductInfoEventListener.php 2020-02-18
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2020 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\Product\Tabs\Listener;

use Gambio\Shop\SellingUnit\Database\Unit\Events\Interfaces\OnGetProductInfoEventInterface;
use Gambio\Shop\SellingUnit\Unit\Entities\Collections\TabCollection;
use Gambio\Shop\SellingUnit\Unit\Entities\Tab;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\TabContent;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\TabTitle;

/**
 * Class OnGetProductInfoEventListener
 * @package Gambio\Shop\Product\Tabs\Listener
 */
class OnGetProductInfoEventListener
{
    /**
     * @param OnGetProductInfoEventInterface $event
     */
    public function __invoke(OnGetProductInfoEventInterface $event)
    {
        $product     = $event->product();
        $tabs = $this->tabs($product->getProductTabs());
        
        $event->builder()->withTabs($tabs);
    }
    
    
    /**
     * @param array $tabsData
     *
     * @return TabCollection
     */
    protected function tabs(array $tabsData) : TabCollection
    {
        $tabs = [];
        foreach ($tabsData as $title => $content) {
            $tabs[] = new Tab(
                new TabTitle($title),
                new TabContent($content)
            );
        }
        return new TabCollection($tabs);
    }
    
}