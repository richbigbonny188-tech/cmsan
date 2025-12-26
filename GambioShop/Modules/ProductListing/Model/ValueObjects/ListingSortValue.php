<?php
/* --------------------------------------------------------------
   ListingSortValue.php 2023-01-13
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
 * Class ListingSortValue
 *
 * @package Gambio\Shop\Modules\ProductListing\Model\ValueObjects
 */
class ListingSortValue
{
    private string $value;
    
    
    /**
     * ListingSortValue constructor.
     *
     * @param string $value
     */
    private function __construct(string $value)
    {
        $this->value = $value;
    }
    
    
    /**
     * @return string
     */
    public function value(): string
    {
        return $this->value;
    }
    
    
    /**
     * Factory method to create a sort value based on the product id.
     *
     * @return static
     */
    public static function id(): self
    {
        return new static('p.products_id');
    }
    
    
    /**
     * Factory method to create a sort value based on the product price.
     *
     * @return static
     */
    public static function price(): self
    {
        return new static('p.products_price');
    }
    
    
    /**
     * Factory method to create a sort value based on the product name.
     *
     * @return static
     */
    public static function name(): self
    {
        return new static('pd.products_name');
    }
    
    
    /**
     * Factory method to create a sort value based on the product start page sort.
     *
     * @return static
     */
    public static function startPage(): self
    {
        return new static('p.products_startpage_sort');
    }
    
    
    /**
     * Factory method to create a sort value based on the product available date.
     *
     * @return static
     */
    public static function dateAvailable(): self
    {
        return new static('p.products_date_available');
    }
    
    
    /**
     * @return static
     */
    public static function crossSell(): static
    {
        return new static('xp.sort_order');
    }
}