<?php
/*--------------------------------------------------------------------------------------------------
    ModifierName.php 2020-01-23
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */
namespace Gambio\Shop\ProductModifiers\Modifiers\ValueObjects;

/**
 * Class ModifierName
 * @package Gambio\Shop\ProductModifiers\Modifiers\ValueObjects
 */
class ModifierName
{
    /**
     * @var string
     */
    protected $name;
    
    
    /**
     * ModifierName constructor.
     *
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }
    
    
    /**
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }
    
}