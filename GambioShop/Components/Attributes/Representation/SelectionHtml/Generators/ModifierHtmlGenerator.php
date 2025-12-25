<?php
/*--------------------------------------------------------------------------------------------------
    ModifierHtmlGenerator.php 2020-07-08
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------------------------------------*/


declare(strict_types=1);

namespace Gambio\Shop\Attributes\Representation\SelectionHtml\Generators;

use Gambio\Shop\Attributes\Representation\SelectionHtml\Repository\DTO\AttributeNameAndValueDTO;
use Gambio\Shop\ProductModifiers\Modifiers\ValueObjects\ModifierIdentifierInterface;
use Gambio\Shop\SellingUnit\Presentation\ValueObjects\OutOfStockMarkings;
use Gambio\Shop\SellingUnit\Unit\Exceptions\InsufficientQuantityException;

use Gambio\Shop\SellingUnit\Unit\ValueObjects\Interfaces\QuantityInterface;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\SellingUnitStockInterface;

/**
 * Class ModifierHtmlGenerator
 * @package Gambio\Shop\Attributes\Representation\SelectionHtml\Generators
 */
class ModifierHtmlGenerator
{
    /**
     * @var AttributeNameAndValueDTO[]
     */
    protected $values = [];
    
    /**
     * @var QuantityInterface
     */
    protected $selectedQuantity;
    
    /**
     * @var SellingUnitStockInterface
     */
    protected $sellingUnitStock;
    /**
     * @var OutOfStockMarkings
     */
    private $outOfStockMarkings;
    
    /**
     * @var bool
     */
    private $isAttributeStockCheckActive;
    
    
    /**
     * ModifierHtmlGenerator constructor.
     *
     * @param AttributeNameAndValueDTO[] $dtos
     * @param QuantityInterface           $selectedQuantity
     * @param SellingUnitStockInterface  $sellingUnitStock
     * @param OutOfStockMarkings         $outOfStockMarkings
     */
    public function __construct(
        array $dtos,
        QuantityInterface $selectedQuantity,
        SellingUnitStockInterface $sellingUnitStock,
        OutOfStockMarkings $outOfStockMarkings,
        bool $isAttributeStockCheckActive
    ) {
        if (count($dtos)) {
    
            foreach ($dtos as $dto) {
                
                $this->addDto($dto);
            }
        }
        $this->selectedQuantity            = $selectedQuantity;
        $this->sellingUnitStock            = $sellingUnitStock;
        $this->outOfStockMarkings          = $outOfStockMarkings;
        $this->isAttributeStockCheckActive = $isAttributeStockCheckActive;
    }
    
    
    /**
     * @param AttributeNameAndValueDTO $dto
     */
    public function addDto(AttributeNameAndValueDTO $dto): void
    {
        $this->values[] = $dto;
    }
    
    /**
     * @return string
     */
    public function toHtml(): string
    {
        $result = '';
        
        if (count($this->values)) {
    
            $quantityHasInsufficientQuantityException = $this->quantityHasInsufficientQuantityException();
            
            foreach ($this->values as $dto) {
                
                $result .= $dto->name() . ': ' . $dto->value();
    
                if ($quantityHasInsufficientQuantityException
                    && $this->modifierHasAnInsufficientQuantity($dto->identifier())) {
                    
                    $result .= $this->outOfStockMarkings->value();
                }
                
                $result .= '<br />';
            }
            
        }
        
        return $result;
    }
    
    
    /**
     * @return bool
     */
    protected function quantityHasInsufficientQuantityException(): bool
    {
        $result            = false;
        $previousException = $this->selectedQuantity->exception();
        
        if ($previousException !== null) {
            
            $result = $previousException instanceof InsufficientQuantityException;
            
            while(($previousException = $previousException->getPrevious()) !== null && $result !== true) {
                
                $result = $previousException instanceof InsufficientQuantityException;
            }
        }
        
        return $result;
    }
    
    
    /**
     * @param ModifierIdentifierInterface $identifier
     *
     * @return bool
     */
    protected function modifierHasAnInsufficientQuantity(ModifierIdentifierInterface $identifier): bool
    {
        return $this->isAttributeStockCheckActive
               && $this->sellingUnitStock->getQuantityByModifier($identifier)->value() <= 0;
    }
}