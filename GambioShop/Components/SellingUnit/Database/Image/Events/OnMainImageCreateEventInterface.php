<?php
/*--------------------------------------------------------------------
 OnMainImageCreateEventInterface.php 2020-2-14
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/
namespace Gambio\Shop\SellingUnit\Database\Image\Events;

use Gambio\Shop\Language\ValueObjects\LanguageId;
use Gambio\Shop\Product\ValueObjects\ProductId;
use Gambio\Shop\ProductModifiers\Modifiers\Collections\ModifierIdentifierCollectionInterface;
use Gambio\Shop\SellingUnit\Images\Builders\CollectionBuilderInterface;

/**
 * Interface OnMainImageCreateEventInterface
 * @package Gambio\Shop\SellingUnit\Database\Image\Events
 */
interface OnMainImageCreateEventInterface
{
    /**
     * @return ProductId
     */
    public function productId(): ProductId;
    
    /**
     * @return ModifierIdentifierCollectionInterface
     */
    public function modifiers(): ModifierIdentifierCollectionInterface;
    
    
    /**
     * @return LanguageId
     */
    public function languageId(): LanguageId;
    
    /**
     * @return CollectionBuilderInterface
     */
    public function builder() : CollectionBuilderInterface;
}