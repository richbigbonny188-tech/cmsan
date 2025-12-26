<?php
/*--------------------------------------------------------------
   SoldProduct.php 2023-09-26
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Statistics\Submodules\SoldProduct\Model;

use Gambio\Admin\Modules\Statistics\Submodules\SoldProduct\Model\Entities\Category;
use Gambio\Admin\Modules\Statistics\Submodules\SoldProduct\Model\ValueObjects\ProductId;
use Webmozart\Assert\Assert;

/**
 * Class SoldProduct
 *
 * @package Gambio\Admin\Modules\Statistics\Submodules\SoldProduct\Model
 */
class SoldProduct
{
    /**
     * SoldProduct constructor.
     *
     * @param ProductId $productId
     * @param float     $orderCount
     * @param string    $name
     * @param Category  $category
     */
    private function __construct(
        private ProductId $productId,
        private float     $orderCount,
        private string    $name,
        private Category  $category
    ) {
    }
    
    
    /**
     * @param ProductId $productId
     * @param float     $orderCount
     * @param string    $name
     * @param Category  $category
     *
     * @return SoldProduct
     */
    public static function create(
        ProductId $productId,
        float     $orderCount,
        string    $name,
        Category  $category
    ): SoldProduct {
        Assert::greaterThanEq($orderCount, 0, 'Expected an order count greater than or equal to %2$s. Got: %s');
        
        return new self($productId, $orderCount, $name, $category);
    }
    
    
    /**
     * @return int
     */
    public function productId(): int
    {
        return $this->productId->value();
    }
    
    
    /**
     * @return float
     */
    public function orderCount(): float
    {
        return $this->orderCount;
    }
    
    
    /**
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }
    
    
    /**
     * @return Category
     */
    public function category(): Category
    {
        return $this->category;
    }
    
    
    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'productId'    => $this->productId(),
            'productName'  => $this->name(),
            'orderCount'   => $this->orderCount(),
            'categoryName' => $this->category->name(),
        ];
    }
}