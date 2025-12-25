<?php
/**
 * OnMainImageCreateEvent.php 2020-08-05
 * Gambio GmbH
 * http://www.gambio.de
 * Copyright (c) 2020 Gambio GmbH
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
 */
namespace Gambio\Shop\SellingUnit\Database\Image\Events;

use Gambio\Shop\Language\ValueObjects\LanguageId;
use Gambio\Shop\Product\ValueObjects\ProductId;
use Gambio\Shop\ProductModifiers\Modifiers\Collections\ModifierIdentifierCollectionInterface;
use Gambio\Shop\SellingUnit\Core\Events\SellingUnitEventTrait;
use Gambio\Shop\SellingUnit\Database\Image\Events\OnMainImageCreateEventInterface;
use Gambio\Shop\SellingUnit\Images\Builders\CollectionBuilder;
use Gambio\Shop\SellingUnit\Images\Builders\CollectionBuilderInterface;

/**
 * Class OnCollectionCreateEvent
 * @package Gambio\Shop\SellingUnit\Images\Events
 */
class OnMainImageCreateEvent implements OnMainImageCreateEventInterface
{
    use SellingUnitEventTrait;
    
    /**
     * @var CollectionBuilder
     */
    protected $builder;
    /**
     * @var ProductId
     */
    protected $productId;
    
    /**
     * @var ModifierIdentifierCollectionInterface
     */
    protected $modifiers;
    /**
     * @var LanguageId
     */
    protected $languageId;
    
    
    /**
     * OnCollectionCreateEvent constructor.
     *
     * @param                                       $product
     * @param ProductId                             $productId
     * @param ModifierIdentifierCollectionInterface $modifiers
     * @param LanguageId                            $languageId
     * @param CollectionBuilderInterface            $collectionBuilder
     */
    public function __construct( $product, ProductId $productId, ModifierIdentifierCollectionInterface $modifiers, LanguageId $languageId, CollectionBuilderInterface $collectionBuilder)
    {
        $this->product    = $product;
        $this->productId  = $productId;
        $this->modifiers  = $modifiers;
        $this->builder    = $collectionBuilder ?? new CollectionBuilder;
        $this->languageId = $languageId;
    }
    
    
    /**
     * @inheritDoc
     */
    public function builder(): CollectionBuilderInterface
    {
        return $this->builder;
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
    public function modifiers(): ModifierIdentifierCollectionInterface
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