<?php
/* --------------------------------------------------------------
   ListingItemPriceValue.php 2023-03-28
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Shop\Modules\ProductListing\Model\ValueObjects;

/**
 * Class ListingItemPriceValue
 *
 * @Todo    : Either add tests for journal context or removed $context from signatures
 *
 * @package Gambio\Shop\Modules\ProductListing\Model\ValueObjects
 */
class ListingItemPriceValue
{
    /**
     * @var string[]
     */
    private array $breakdown;
    private float $value;
    
    
    /**
     * ListingItemPriceValue constructor.
     *
     * @param float $value
     * @param array $breakdown
     */
    private function __construct(float $value, array $breakdown)
    {
        $this->value     = $value;
        $this->breakdown = $breakdown;
    }
    
    
    /**
     * Creates a new listing item price value instance with an empty journal.
     *
     * @param float $value
     *
     * @return self
     */
    public static function create(float $value): self
    {
        return new self($value, []);
    }
    
    
    public static function empty(): self
    {
        return self::create(0.0);
    }
    
    
    /**
     * @return float
     */
    public function value(): float
    {
        return $this->value;
    }
    
    
    /**
     * @return array
     */
    public function breakdown(): array
    {
        return $this->breakdown;
    }
    
    
    /**
     * Add $value to price, returning a new instance containing the result.
     *
     * @param float       $value
     * @param string|null $context
     *
     * @return self
     */
    public function add(float $value, string $context = null): self
    {
        $newValue  = $this->value + $value;
        $breakdown = "Add '$value' to '$this->value' results in '$newValue'";
        if ($context) {
            $breakdown .= " ($context)";
        }
        
        return $this->createNew($newValue, $breakdown);
    }
    
    
    /**
     * Subtract $value from the price, returning a new instance containing the result.
     *
     * @param float       $value
     * @param string|null $context
     *
     * @return self
     */
    public function subtract(float $value, string $context = null): self
    {
        $newValue  = $this->value - $value;
        $breakdown = "Subtract '$value' from '$this->value' results in '$newValue'";
        if ($context) {
            $breakdown .= " ($context)";
        }
        
        return $this->createNew($newValue, $breakdown);
    }
    
    
    /**
     * Multiplies price by $value, returning a new instance containing the result.
     *
     * @param float       $value
     * @param string|null $context
     *
     * @return self
     */
    public function multiply(float $value, string $context = null): self
    {
        $newValue  = $this->value * $value;
        $breakdown = "Multiply '$this->value' by '$value' results in '$newValue'";
        if ($context) {
            $breakdown .= " ($context)";
        }
        
        return $this->createNew($newValue, $breakdown);
    }
    
    
    /**
     * Divides price by $value, returning a new instance containing the result.
     *
     * @param float       $value
     * @param string|null $context
     *
     * @return self
     */
    public function divide(float $value, string $context = null): self
    {
        $newValue  = $this->value / $value;
        $breakdown = "Divide '$this->value' by '$value' results in '$newValue'";
        if ($context) {
            $breakdown .= " ($context)";
        }
        
        return $this->createNew($newValue, $breakdown);
    }
    
    
    public function round(int $precision, string $context = null): self
    {
        $newValue  = round($this->value, $precision);
        $breakdown = "Rounding '$this->value' with precision '$precision' results in '$newValue'";
        if ($context) {
            $breakdown .= " ($context)";
        }
    
        return $this->createNew($newValue, $breakdown);
    }
    
    
    /**
     * Creates a new listing item price instance with the new value and merged journal.
     *
     * @param float  $newValue
     * @param string $breakdown
     *
     * @return self
     */
    private function createNew(float $newValue, string $breakdown): self
    {
        $journal = array_merge($this->breakdown, [$breakdown]);
        
        return new self($newValue, $journal);
    }
}