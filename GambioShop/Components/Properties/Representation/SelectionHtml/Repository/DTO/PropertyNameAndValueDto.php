<?php
/**
 * PropertyNameAndValueDto.php 2020-3-17
 * Gambio GmbH
 * http://www.gambio.de
 * Copyright (c) 2020 Gambio GmbH
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
 */

declare(strict_types=1);

namespace Gambio\Shop\Properties\Representation\SelectionHtml\Repository\DTO;

/**
 * Class PropertyNameAndValueDto
 * @package Gambio\Shop\Properties\Representation\SelectionHtml\Repository\DTO
 */
class PropertyNameAndValueDto
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
     * PropertyNameAndValueDto constructor.
     *
     * @param string $name
     * @param string $value
     */
    public function __construct(string $name, string $value)
    {
        $this->name  = $name;
        $this->value = $value;
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
}