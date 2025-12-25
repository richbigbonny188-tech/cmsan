<?php
/*------------------------------------------------------------------------------
 OnGetSellingUnitModelEventListener.php 2022-09-15
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2022 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -----------------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\Properties\Database\Listeners;

use Gambio\Shop\Product\Model\Criteria\ShowModelCriteria;
use Gambio\Shop\Properties\Database\Services\Interfaces\PropertiesReaderServiceInterface;
use Gambio\Shop\Properties\Database\Services\PropertiesReaderService;
use Gambio\Shop\SellingUnit\Database\Unit\Events\OnGetSellingUnitModelEvent;

/**
 * Class OnGetSellingUnitModelEventListener
 *
 * @package Gambio\Shop\Properties\SellingUnitModel\Database\Listener
 */
class OnGetSellingUnitModelEventListener
{
    /**
     * @var PropertiesReaderService
     */
    protected $service;

    /**
     * @var bool
     */
    protected $appendPropertiesModel;
    
    /**
     * @var ShowModelCriteria
     */
    private $criteria;
    
    
    /**
     * @param PropertiesReaderServiceInterface $service
     * @param bool                             $appendPropertiesModel
     * @param ShowModelCriteria                $criteria
     */
    public function __construct(
        PropertiesReaderServiceInterface $service,
        bool $appendPropertiesModel,
        ShowModelCriteria $criteria
    ) {
        $this->service               = $service;
        $this->appendPropertiesModel = $appendPropertiesModel;
        $this->criteria              = $criteria;
    }


    /**
     *
     * @param OnGetSellingUnitModelEvent $event
     *
     * @return OnGetSellingUnitModelEvent
     */
    public function __invoke(OnGetSellingUnitModelEvent $event)
    {
        $combination = $this->service->getCombinationFor($event->id());

        if ($combination && $combination->model()->value() !== '') {
            if ($this->appendPropertiesModel === false) {
                $event->builder()->wipeData()->withModelAtPos($combination->model()->value(), 3000)
                    ->withShowInShoppingCartAndWishlist($this->criteria->showInShoppingCartAndWishlist())
                    ->withShowInProductLists($this->criteria->showInProductLists())
                    ->withShowInProductDetails($this->criteria->showInProductDetails());
                $event->stopPropagation();
                return $event;
            }
            $event->builder()->withModelAtPos($combination->model()->value(), 3000)
                ->withShowInShoppingCartAndWishlist($this->criteria->showInShoppingCartAndWishlist())
                ->withShowInProductLists($this->criteria->showInProductLists())
                ->withShowInProductDetails($this->criteria->showInProductDetails());
        }

    }

}