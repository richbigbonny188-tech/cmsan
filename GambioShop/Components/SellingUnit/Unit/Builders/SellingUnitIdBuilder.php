<?php
/*--------------------------------------------------------------------
 SellingUnitIdBuilder.php 2020-2-25
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/
namespace Gambio\Shop\SellingUnit\Unit\Builders;

use Gambio\Shop\Language\ValueObjects\LanguageId;
use Gambio\Shop\Product\ValueObjects\ProductId;
use Gambio\Shop\ProductModifiers\Modifiers\Collections\ModifierIdentifierCollection;
use Gambio\Shop\ProductModifiers\Modifiers\Collections\ModifierIdentifierCollectionInterface;
use Gambio\Shop\ProductModifiers\Modifiers\ValueObjects\ModifierIdentifierInterface;
use Gambio\Shop\SellingUnit\Unit\Builders\Interfaces\SellingUnitIdBuilderInterface;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\SellingUnitId;

/**
 * Class SellingUnitIdBuilder
 * @package Gambio\Shop\SellingUnit\Unit\Builders
 */
class SellingUnitIdBuilder implements SellingUnitIdBuilderInterface
{
    /**
     * @var LanguageId
     */
    private $languageId;
    /**
     * @var ModifierIdentifierCollectionInterface
     */
    private $modifierIdCollection;
    /**
     * @var ProductId
     */
    private $productId;
    
    public function __construct()
    {
        $this->modifierIdCollection = new ModifierIdentifierCollection();
    }
    
    
    /**
     * @inheritDoc
     */
    public function withProductId(ProductId $id): SellingUnitIdBuilderInterface
    {
        $this->productId = $id;
        return $this;
    }
    
    
    /**
     * @inheritDoc
     */
    public function withModifierId(ModifierIdentifierInterface $id): SellingUnitIdBuilderInterface
    {
        $this->modifierIdCollection[] = $id;
        return $this;
    }
    
    
    /**
     * @inheritDoc
     */
    public function withLanguageId(LanguageId $id): SellingUnitIdBuilderInterface
    {
        $this->languageId = $id;
        return $this;
    }
    
    
    /**
     * @inheritDoc
     */
    public function build(): SellingUnitId
    {
        return new SellingUnitId($this->modifierIdCollection, $this->productId, $this->languageId);
    }
}