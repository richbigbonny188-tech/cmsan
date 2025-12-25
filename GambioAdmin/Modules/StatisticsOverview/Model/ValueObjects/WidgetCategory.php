<?php
/*--------------------------------------------------------------
   WidgetCategory.php 2022-05-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects;

use Webmozart\Assert\Assert;

/**
 * Class representing a widget category.
 *
 * @package Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects
 */
class WidgetCategory
{
    /**
     * Customers category name.
     */
    const CUSTOMERS = "customers";
    
    /**
     * Orders category name.
     */
    const ORDERS = "orders";
    
    /**
     * System category name.
     */
    const SYSTEM = "system";
    
    /**
     * Valid categories.
     */
    private const VALID_VALUES = [
        self::CUSTOMERS,
        self::ORDERS,
        self::SYSTEM
    ];
    
    /**
     * Value.
     *
     * @var string
     */
    private $value;
    
    
    /**
     * Constructor.
     *
     * @param string $value Value.
     */
    private function __construct(string $value)
    {
        Assert::stringNotEmpty($value);
        Assert::inArray($value, self::VALID_VALUES);
        
        $this->value = $value;
    }
    
    
    /**
     * Create instance.
     *
     * @param string $category Value.
     *
     * @return WidgetCategory Instance.
     */
    public static function create(string $category): self
    {
        return new self($category);
    }
    
    
    /**
     * Return value.
     *
     * @return string Value.
     */
    public function value(): string
    {
        return $this->value;
    }
}