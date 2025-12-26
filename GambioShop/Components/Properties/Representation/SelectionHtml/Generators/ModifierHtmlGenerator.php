<?php
/**
 * ModifierHtmlGenerator.php 2020-3-17
 * Gambio GmbH
 * http://www.gambio.de
 * Copyright (c) 2020 Gambio GmbH
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
 */

declare(strict_types=1);

namespace Gambio\Shop\Properties\Representation\SelectionHtml\Generators;

use Gambio\Shop\Properties\Representation\SelectionHtml\Repository\DTO\PropertyNameAndValueDto;

/**
 * Class ModifierHtmlGenerator
 * @package Gambio\Shop\Properties\Representation\SelectionHtml\Generators
 */
class ModifierHtmlGenerator
{
    /**
     * @var PropertyNameAndValueDto[]
     */
    protected $values = [];
    
    /**
     * ModifierHtmlGenerator constructor.
     *
     * @param PropertyNameAndValueDto[] $dtos
     */
    public function __construct(array $dtos)
    {
        if (count($dtos)) {
            
            foreach ($dtos as $dto) {
                
                $this->addDto($dto);
            }
        }
    }
    
    
    /**
     * @param PropertyNameAndValueDto $dto
     */
    public function addDto(PropertyNameAndValueDto $dto): void
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
            
            foreach ($this->values as $dto) {
                
                $result .= $dto->name() . ': ' . $dto->value() . '<br />';
            }
            
        }
        
        return $result;
    }
}