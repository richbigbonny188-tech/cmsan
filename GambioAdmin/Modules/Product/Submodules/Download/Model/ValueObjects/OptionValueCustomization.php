<?php
/*--------------------------------------------------------------------
 OptionValueCustomization.php 2023-06-21
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2023 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\Download\Model\ValueObjects;

/**
 * Class OptionValueCustomization
 *
 * @package Gambio\Admin\Modules\Product\Submodules\Download\Model\ValueObjects
 */
class OptionValueCustomization
{
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
        return new static($modelNumber, $weight, $price);
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
        return new static($this->modelNumber(), $this->weight(), $price);
    }
    
    
    /**
     * @param string $modelNumber
     *
     * @return $this
     */
    public function withModelNumber(string $modelNumber): OptionValueCustomization
    {
        return new static($modelNumber, $this->weight(), $this->price());
    }
    
    
    /**
     * @param float $weight
     *
     * @return $this
     */
    public function withWeight(float $weight): OptionValueCustomization
    {
        return new static($this->modelNumber(), $weight, $this->price());
    }
}