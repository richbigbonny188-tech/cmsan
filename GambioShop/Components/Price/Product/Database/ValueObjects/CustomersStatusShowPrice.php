<?php
/**
 * CustomersStatusShowPrice.php 2020-3-23
 * Gambio GmbH
 * http://www.gambio.de
 * Copyright (c) 2020 Gambio GmbH
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
 */

declare(strict_types=1);

namespace Gambio\Shop\Price\Product\Database\ValueObjects;

/**
 * Class CustomersStatusShowPrice
 * @package Gambio\Shop\Price\Product\Database\ValueObjects
 */
class CustomersStatusShowPrice
{
    /**
     * @var bool
     */
    protected $status;
    
    
    /**
     * CustomersStatusShowPrice constructor.
     *
     * @param bool $status
     */
    public function __construct(bool $status)
    {
        $this->status = $status;
    }
    
    
    /**
     * @return bool
     */
    public function value(): bool
    {
        return $this->status;
    }
}