<?php
/*--------------------------------------------------------------------------------------------------
    ProductId.php 2020-07-08
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\Product\ValueObjects;

/**
 * Class ProductId
 * @package Gambio\Shop\ProductModifiers\ValueObjects
 */
class ProductId
{
    /**
     * @var int
     */
    protected $value;
    
    
    /**
     * ProductId constructor.
     *
     * @param int $value
     *
     * @throws \Exception
     */
    public function __construct(int $value)
    {
        if (!$value) {
            throw new \Exception("Product ID can't be 0");
        }
        $this->value = $value;
    }
    
    
    /**
     * @return int
     */
    public function value(): int
    {
        return $this->value;
    }

    public function equals(?ProductId $id): bool
    {
        return $id && $id->value() === $this->value();

    }

}