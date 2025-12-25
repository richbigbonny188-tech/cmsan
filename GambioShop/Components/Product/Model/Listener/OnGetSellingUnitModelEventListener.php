<?php
/*--------------------------------------------------------------------
 OnGetProductModelEventListener.php 2022-04-26
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2022 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\Product\Model\Listener;

use Gambio\Shop\Product\Model\Criteria\ShowModelCriteria;
use Gambio\Shop\SellingUnit\Database\Unit\Events\Interfaces\OnGetSellingUnitModelEventInterface;

/**
 * Class OnGetProductModelEventListener
 *
 * @package Gambio\Shop\Product\Model\Listener
 */
class OnGetSellingUnitModelEventListener
{
    /**
     * @var ShowModelCriteria
     */
    private $criteria;
    
    
    public function __construct(ShowModelCriteria $criteria)
    {
        $this->criteria = $criteria;
    }
    
    
    /**
     * @param OnGetSellingUnitModelEventInterface $event
     *
     * @return OnGetSellingUnitModelEventInterface
     */
    public function __invoke(OnGetSellingUnitModelEventInterface $event)
    {
        if ($event->product()->getModel()) {
            $event->builder()->withModelAtPos($event->product()->getModel(), 1000);
        }
        
        $event->builder()
            ->withShowInShoppingCartAndWishlist($this->criteria->showInShoppingCartAndWishlist())
            ->withShowInProductLists($this->criteria->showInProductLists())
            ->withShowInProductDetails($this->criteria->showInProductDetails());
        
        return $event;
    }
}