<?php
/*--------------------------------------------------------------------------------------------------
    Repository.php 2020-07-08
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\Attributes\Representation\SelectionHtml\Repository;

use Gambio\Shop\Attributes\ProductModifiers\Database\ValueObjects\AttributeModifierIdentifier;
use Gambio\Shop\Attributes\Representation\SelectionHtml\Generators\ModifierHtmlGenerator;
use Gambio\Shop\Attributes\Representation\SelectionHtml\Repository\Readers\ReaderInterface;
use Gambio\Shop\Language\ValueObjects\LanguageId;
use Gambio\Shop\ProductModifiers\Modifiers\Collections\ModifierIdentifierCollectionInterface;
use Gambio\Shop\SellingUnit\Presentation\ValueObjects\OutOfStockMarkings;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\Interfaces\QuantityInterface;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\SellingUnitStockInterface;

/**
 * Class Repository
 * @package Gambio\Shop\Attributes\Representation\SelectionHtml\Repository
 */
class Repository implements RepositoryInterface
{
    /**
     * @var ReaderInterface
     */
    protected $reader;
    /**
     * @var OutOfStockMarkings
     */
    private $outOfStockMarkings;
    /**
     * @var bool
     */
    private $isAttributeStockCheckActive;
    
    
    /**
     * Repository constructor.
     *
     * @param ReaderInterface    $reader
     * @param OutOfStockMarkings $outOfStockMarkings
     * @param bool               $isAttributeStockCheckActive
     */
    public function __construct(
        ReaderInterface $reader,
        OutOfStockMarkings $outOfStockMarkings,
        bool $isAttributeStockCheckActive
    ) {
        $this->reader                      = $reader;
        $this->outOfStockMarkings          = $outOfStockMarkings;
        $this->isAttributeStockCheckActive = $isAttributeStockCheckActive;
    }
    
    
    /**
     * @inheritDoc
     */
    public function selectionHtmlGenerator(
        ModifierIdentifierCollectionInterface $identifiers,
        LanguageId $languageId,
        QuantityInterface $selectedQuantity,
        SellingUnitStockInterface $sellingUnitStock
    ): ModifierHtmlGenerator {
    
        $dtos = [];
        
        foreach ($identifiers as $identifier) {
            
            if ($identifier instanceof AttributeModifierIdentifier) {
                
                $dtos[] = $this->reader->selectionData($identifier, $languageId);
            }
        }
        
        return $this->createGenerator($dtos, $selectedQuantity, $sellingUnitStock);
    }
    
    
    /**
     * @param array                     $dtos
     *
     * @param QuantityInterface         $selectedQuantity
     * @param SellingUnitStockInterface $sellingUnitStock
     *
     * @return ModifierHtmlGenerator
     */
    protected function createGenerator(
        array $dtos,
        QuantityInterface $selectedQuantity,
        SellingUnitStockInterface $sellingUnitStock
    ): ModifierHtmlGenerator {
        
        return new ModifierHtmlGenerator($dtos,
                                         $selectedQuantity,
                                         $sellingUnitStock,
                                         $this->outOfStockMarkings,
                                         $this->isAttributeStockCheckActive);
    }
}