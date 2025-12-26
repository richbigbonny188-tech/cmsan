<?php
/* --------------------------------------------------------------
   OptionValueStock.php 2021-11-10
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Option\Model\ValueObjects;

use Webmozart\Assert\Assert;

/**
 * Class OptionValueStock
 *
 * @package Gambio\Admin\Modules\Option\Model\ValueObjects
 * @codeCoverageIgnore
 */
class OptionValueStock
{
    private const HIGHEST_STORABLE_FLOAT = 99999.9999;
    private const MAXIMUM_DECIMAL_PLACES = 4;
    
    public const POSITIVE_STOCK_TYPE    = 'only-positive';
    public const NATURAL_STOCK_TYPE     = 'all-numbers';
    public const NOT_MANAGED_STOCK_TYPE = 'not-managed';
    public const ALLOWED_STOCK_TYPES    = [
        self::POSITIVE_STOCK_TYPE,
        self::NATURAL_STOCK_TYPE,
        self::NOT_MANAGED_STOCK_TYPE,
    ];
    
    /**
     * @var string
     */
    private $stockType;
    
    /**
     * @var float
     */
    private $stock;
    
    /**
     * @var bool
     */
    private $stockCentrallyManaged;
    
    
    /**
     * OptionValueStock constructor.
     *
     * @param string $stockType
     * @param float  $stock
     * @param bool   $stockCentrallyManaged
     */
    private function __construct(string $stockType, float $stock, bool $stockCentrallyManaged)
    {
        $this->stockType             = $stockType;
        $this->stock                 = $stock;
        $this->stockCentrallyManaged = $stockCentrallyManaged;
    }
    
    
    /**
     * @param string $stockType
     * @param float  $stock
     * @param bool   $stockCentrallyManaged
     *
     * @return OptionValueStock
     */
    public static function create(string $stockType, float $stock, bool $stockCentrallyManaged): OptionValueStock
    {
        $stock = round($stock, self::MAXIMUM_DECIMAL_PLACES, PHP_ROUND_HALF_UP);
        
        $message = 'Stock\'s value must be less than or equal to %2$s. Got: %s';
        Assert::lessThanEq($stock, self::HIGHEST_STORABLE_FLOAT, $message);
        
        Assert::oneOf($stockType,
                      self::ALLOWED_STOCK_TYPES,
                      'Invalid type given. Need to be one of: ' . implode(', ', self::ALLOWED_STOCK_TYPES)
                      . '; Got: %s');
        
        return new self($stockType, $stock, $stockCentrallyManaged);
    }
    
    
    /**
     * @return string
     */
    public function stockType(): string
    {
        return $this->stockType;
    }
    
    
    /**
     * @return float
     */
    public function stock(): float
    {
        return $this->stock;
    }
    
    
    /**
     * @return bool
     */
    public function isStockCentrallyManaged(): bool
    {
        return $this->stockCentrallyManaged;
    }
}