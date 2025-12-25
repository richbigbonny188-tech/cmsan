<?php
/*--------------------------------------------------------------------
 OnGetProductInfoEventInterface.php 2020-2-24
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/
namespace Gambio\Shop\SellingUnit\Database\Unit\Events\Interfaces;

use Gambio\Shop\Language\ValueObjects\LanguageId;
use Gambio\Shop\Product\ValueObjects\ProductId;
use Gambio\Shop\ProductModifiers\Modifiers\Collections\ModifierIdentifierCollectionInterface;
use Gambio\Shop\SellingUnit\Unit\Builders\Interfaces\ProductInfoBuilderInterface;
use PriceDataInterface;
use ProductDataInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * Interface OnGetProductInfoEventInterface
 * @package Gambio\Shop\SellingUnit\Database\Unit\Events\Interfaces
 */
interface OnGetProductInfoEventInterface
{
    /**
     * @return ProductInfoBuilderInterface
     */
    public function builder(): ProductInfoBuilderInterface;
    
    
    /**
     * @return ProductDataInterface
     */
    public function product(): ProductDataInterface;
    
    /**
     * @return EventDispatcherInterface
     */
    public function dispatcher(): EventDispatcherInterface;
    
    
    /**
     * @return ProductId
     */
    public function productId(): ProductId;
    
    
    /**
     * @return ModifierIdentifierCollectionInterface
     */
    public function modifierIdentifiers(): ModifierIdentifierCollectionInterface;
    
    
    /**
     * @return LanguageId
     */
    public function languageId(): LanguageId;
}