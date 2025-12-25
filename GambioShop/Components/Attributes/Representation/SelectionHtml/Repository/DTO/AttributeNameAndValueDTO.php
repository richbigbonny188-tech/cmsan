<?php
/**
 * AttributeNameAndValueDTO.php 2020-3-19
 * Gambio GmbH
 * http://www.gambio.de
 * Copyright (c) 2020 Gambio GmbH
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
 */

declare(strict_types=1);

namespace Gambio\Shop\Attributes\Representation\SelectionHtml\Repository\DTO;

use Gambio\Shop\ProductModifiers\Modifiers\ValueObjects\ModifierIdentifierInterface;

/**
 * Class AttributeNameAndValueDTO
 * @package Gambio\Shop\Attributes\Representation\SelectionHtml\Repository\DTO
 */
class AttributeNameAndValueDTO
{
    /**
     * @var string
     */
    protected $name;
    
    /**
     * @var string
     */
    protected $value;
    
    /**
     * @var ModifierIdentifierInterface
     */
    protected $identifier;
    
    
    /**
     * AttributeNameAndValueDTO constructor.
     *
     * @param string                      $name
     * @param string                      $value
     * @param ModifierIdentifierInterface $identifier
     */
    public function __construct(string $name, string $value, ModifierIdentifierInterface $identifier)
    {
        $this->name       = $name;
        $this->value      = $value;
        $this->identifier = $identifier;
    }
    
    
    /**
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }
    
    
    /**
     * @return string
     */
    public function value(): string
    {
        return $this->value;
    }
    
    
    /**
     * @return ModifierIdentifierInterface
     */
    public function identifier(): ModifierIdentifierInterface
    {
        return $this->identifier;
    }
}