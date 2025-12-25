<?php
/*--------------------------------------------------------------------
 OnGetProductInfoEvent.php 2020-2-18
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/
namespace Gambio\Shop\SellingUnit\Database\Unit\Events;

use Gambio\Shop\Language\ValueObjects\LanguageId;
use Gambio\Shop\Product\ValueObjects\ProductId;
use Gambio\Shop\ProductModifiers\Modifiers\Collections\ModifierIdentifierCollectionInterface;
use Gambio\Shop\SellingUnit\Core\Events\SellingUnitEventTrait;
use Gambio\Shop\SellingUnit\Database\Unit\Events\Interfaces\OnGetProductInfoEventInterface;
use Gambio\Shop\SellingUnit\Unit\Builders\Interfaces\ProductInfoBuilderInterface;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\AbstractQuantity;
use PriceDataInterface;
use ProductDataInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * Class OnGetProductInfoEvent
 * @package Gambio\Shop\SellingUnit\Database\Unit\Events
 */
class OnGetProductInfoEvent implements OnGetProductInfoEventInterface
{
    use SellingUnitEventTrait;
    
    /**
     * @var ProductId
     */
    protected $productId;
    
    /**
     * @var ModifierIdentifierCollectionInterface
     */
    protected $modifiers;
    
    /**
     * @var AbstractQuantity
     */
    protected $quantity;
    
    /**
     * @var LanguageId
     */
    protected $languageId;
    
    /**
     * @var ProductInfoBuilderInterface
     */
    protected $builder;
    
    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;
    
    
    /**
     * OnGetProductInfoEvent constructor.
     *
     * @param ProductDataInterface                  $product
     * @param ProductId                             $productId
     * @param ModifierIdentifierCollectionInterface $modifiers
     * @param LanguageId                            $languageId
     * @param ProductInfoBuilderInterface           $builder
     * @param PriceDataInterface                    $xtcPrice
     * @param EventDispatcherInterface              $dispatcher
     */
    public function __construct(
        ProductDataInterface $product,
        ProductId $productId,
        ModifierIdentifierCollectionInterface $modifiers,
        LanguageId $languageId,
        ProductInfoBuilderInterface $builder,
        PriceDataInterface $xtcPrice,
        EventDispatcherInterface $dispatcher
    ) {
    
        $this->product    = $product;
        $this->productId  = $productId;
        $this->modifiers  = $modifiers;
        $this->languageId = $languageId;
        $this->builder    = $builder;
        $this->xtcPrice   = $xtcPrice;
        $this->dispatcher = $dispatcher;
    }
    
    
    /**
     * @inheritDoc
     */
    public function builder(): ProductInfoBuilderInterface
    {
        return $this->builder;
    }
    
    
    /**
     * @return EventDispatcherInterface
     */
    public function dispatcher(): EventDispatcherInterface
    {
        return $this->dispatcher;
    }
    
    
    /**
     * @inheritDoc
     */
    public function productId(): ProductId
    {
        return $this->productId;
    }
    
    
    /**
     * @inheritDoc
     */
    public function modifierIdentifiers(): ModifierIdentifierCollectionInterface
    {
        return $this->modifiers;
    }
    
    
    /**
     * @inheritDoc
     */
    public function languageId(): LanguageId
    {
        return $this->languageId;
    }
}