<?php
/*--------------------------------------------------------------------------------------------------
    CombinationQuantity.php 2020-02-21
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */
namespace Gambio\Shop\Properties\Properties\ValueObjects;

class CombinationQuantity
{
    /**
     * @var float
     */
    private $value;
    
    
    /**
     * CombinationOrder constructor.
     *
     * @param float $value
     */
    public function __construct(float $value)
    {
        $this->value = $value;
    }
    
    
    /**
     * @return float
     */
    public function value(): float
    {
        return $this->value;
    }
    
}