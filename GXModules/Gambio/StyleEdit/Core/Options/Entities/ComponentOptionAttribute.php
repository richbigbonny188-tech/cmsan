<?php
/*--------------------------------------------------------------------------------------------------
    ComponentOptionAttribute.php 2023-06-06
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2023 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    -----------------------------------------------------------------------------------------------*/

namespace Gambio\StyleEdit\Core\Options\Entities;

/**
 * Class ComponentOptionAttribute
 *
 * @package Gambio\StyleEdit\Core\Options\Entities
 */
class ComponentOptionAttribute
{
    /**
     * @var string
     */
    protected string $name;
    
    /**
     * @var mixed
     */
    protected $value;
    
    
    /**
     * ComponentOptionAttribute constructor.
     *
     * @param string $name
     * @param mixed  $value
     */
    private function __construct(string $name, $value)
    {
        $this->value = $value;
        $this->name  = $name;
    }
    
    
    /**
     * @param string $name
     * @param mixed  $value
     *
     * @return ComponentOptionAttribute
     */
    public static function create(string $name, $value): ComponentOptionAttribute
    {
        return new static($name, $value);
    }
    
    
    /**
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }
    
    
    /**
     * @return mixed
     */
    public function value()
    {
        return $this->value;
    }
}