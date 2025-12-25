<?php
/*--------------------------------------------------------------------------------------------------
    CombinationId.php 2020-02-20
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */
namespace Gambio\Shop\Properties\Properties\ValueObjects;

class CombinationId
{
    /**
     * @var int
     */
    protected $value;
    
    
    /**
     * CombinationId constructor.
     *
     * @param int $value
     */
    public function __construct(int $value)
    {
        $this->value = $value;
    }
    
    
    /**
     * @return int
     */
    public function value(): int
    {
        return $this->value;
    }
    
    
    /**
     * @param CombinationId $value
     *
     * @return bool
     */
    public function equals(CombinationId $value): bool
    {
        return $value && $value->value() === $this->value() && ($value instanceof static);
    }
    
}