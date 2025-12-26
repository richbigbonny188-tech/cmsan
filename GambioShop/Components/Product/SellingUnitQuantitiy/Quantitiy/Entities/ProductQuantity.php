<?php
/*--------------------------------------------------------------------------------------------------
    ProductQuantity.php 2020-08-06
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\Shop\Product\SellingUnitQuantitiy\Quantitiy\Entities;

use Gambio\Shop\ProductModifiers\Modifiers\Collections\ModifierIdentifierCollectionInterface;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\AbstractQuantity;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\ModifierQuantityInterface;

/**
 * Class ProductQuantity
 * @package Gambio\Shop\Product\SellingUnitQuantitiy\Quantitiy\Entities
 */
class ProductQuantity extends AbstractQuantity implements ModifierQuantityInterface
{
    /**
     * @var ModifierIdentifierCollectionInterface
     */
    protected $linkedModifiers;
    
    
    /**
     * ProductQuantity constructor.
     *
     * @param float                                 $quantity
     * @param string                                $measureUnit
     * @param ModifierIdentifierCollectionInterface $collection
     */
    public function __construct(float $quantity, string $measureUnit, ModifierIdentifierCollectionInterface $collection)
    {
        parent::__construct($quantity, $measureUnit);
        
        $this->linkedModifiers = $collection;
    }
    
    
    /**
     * @inheritDoc
     */
    public function linkedModifiers(): ModifierIdentifierCollectionInterface
    {
        return $this->linkedModifiers;
    }
}