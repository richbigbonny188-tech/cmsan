<?php
/*--------------------------------------------------------------------
 OptionValueCustomization.php 2023-06-06
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2023 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects;

use Webmozart\Assert\Assert;

/**
 * Class OptionValueCustomization
 *
 * @package Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects
 */
class OptionValueCustomization
{
    private const HIGHEST_STORABLE_FLOAT = 99999999999.9999;
    private const MAXIMUM_DECIMAL_PLACES = 4;
    
    
    /**
     * OptionValueCustomization constructor.
     *
     * @param string $modelNumber
     * @param float  $weight
     * @param float  $price
     */
    private function __construct(
        private string $modelNumber,
        private float  $weight,
        private float  $price
    ) {
    }
    
    
    /**
     * @param string $modelNumber
     * @param float  $weight
     * @param float  $price
     *
     * @return OptionValueCustomization
     */
    public static function create(
        string $modelNumber,
        float  $weight,
        float  $price
    ): OptionValueCustomization {
        $weight = round($weight, self::MAXIMUM_DECIMAL_PLACES, PHP_ROUND_HALF_UP);
        $price  = round($price, self::MAXIMUM_DECIMAL_PLACES, PHP_ROUND_HALF_UP);
        
        $message = '\'s value must be less than or equal to %2$s. Got: %s';
        
        Assert::lessThanEq($weight, self::HIGHEST_STORABLE_FLOAT, 'Weight' . $message);
        Assert::lessThanEq($price, self::HIGHEST_STORABLE_FLOAT, 'Price' . $message);
        
        return new self($modelNumber, $weight, $price);
    }
    
    
    /**
     * @return string
     */
    public function modelNumber(): string
    {
        return $this->modelNumber;
    }
    
    
    /**
     * @return float
     */
    public function weight(): float
    {
        return $this->weight;
    }
    
    
    /**
     * @return float
     */
    public function price(): float
    {
        return $this->price;
    }
    
    
    /**
     * @param float $price
     *
     * @return OptionValueCustomization
     */
    public function withPrice(float $price): OptionValueCustomization
    {
        return new self($this->modelNumber(), $this->weight(), $price);
    }
    
    
    /**
     * @param string $modelNumber
     *
     * @return $this
     */
    public function withModelNumber(string $modelNumber): OptionValueCustomization
    {
        return new self($modelNumber, $this->weight(), $this->price());
    }
    
    
    /**
     * @param float $weight
     *
     * @return $this
     */
    public function withWeight(float $weight): OptionValueCustomization
    {
        return new self($this->modelNumber(), $weight, $this->price());
    }
}