<?php
/*--------------------------------------------------------------
   ProductVariantStock.php 2023-06-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects;

use InvalidArgumentException;
use Webmozart\Assert\Assert;

/**
 * Class ProductVariantStock
 *
 * @package Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects
 */
class ProductVariantStock
{
    private const HIGHEST_STORABLE_FLOAT = 99999999999.9999;
    private const MAXIMUM_DECIMAL_PLACES = 4;
    
    public const  STOCK_TYPE_ALWAYS_POSITIV  = 'only-positive';
    public const  STOCK_TYPE_MAY_BE_NEGATIVE = 'all-numbers';
    public const  STOCK_TYPE_NOT_MANAGED     = 'not-managed';
    
    private const ALLOWED_STOCK_TYPES = [
        self::STOCK_TYPE_ALWAYS_POSITIV,
        self::STOCK_TYPE_MAY_BE_NEGATIVE,
        self::STOCK_TYPE_NOT_MANAGED,
    ];
    
    /**
     * ProductVariantStock constructor.
     *
     * @param float  $stock
     * @param string $stockType
     */
    private function __construct(private float $stock, private string $stockType) { }
    
    
    /**
     * @param float  $stock
     * @param string $stockType
     *
     * @return ProductVariantStock
     */
    public static function create(
        float  $stock = 0,
        string $stockType = ProductVariantStock::STOCK_TYPE_NOT_MANAGED
    ): ProductVariantStock {
        $stock = round($stock, self::MAXIMUM_DECIMAL_PLACES, PHP_ROUND_HALF_UP);
        
        $message = 'Stock\'s value must be less than or equal to %2$s. Got: %s';
        Assert::lessThanEq($stock, self::HIGHEST_STORABLE_FLOAT, $message);
        
        Assert::oneOf($stockType,
                      self::ALLOWED_STOCK_TYPES,
                      'Stock type must be one of: ' . implode(',', self::ALLOWED_STOCK_TYPES) . '; Got: %s');
        
        if ($stockType === self::STOCK_TYPE_ALWAYS_POSITIV && $stock < 0) {
            throw new InvalidArgumentException('Stock must be greater than or equal to 0, if the stock type only allows positiv values.');
        }
        
        return new self($stock, $stockType);
    }
    
    
    /**
     * @return float
     */
    public function stock(): float
    {
        return $this->stock;
    }
    
    
    /**
     * @return string
     */
    public function stockType(): string
    {
        return $this->stockType;
    }
}
