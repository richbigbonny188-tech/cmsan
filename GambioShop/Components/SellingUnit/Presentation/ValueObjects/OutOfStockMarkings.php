<?php
/**
 * OutOfStockMarkings.php 2020-3-24
 * Gambio GmbH
 * http://www.gambio.de
 * Copyright (c) 2020 Gambio GmbH
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
 */

declare(strict_types=1);

namespace Gambio\Shop\SellingUnit\Presentation\ValueObjects;

/**
 * Class OutOfStockMarkings
 * @package Gambio\Shop\SellingUnit\Presentation\ValueObjects
 */
class OutOfStockMarkings
{
    /**
     * @var string
     */
    protected $markings;
    
    
    /**
     * OutOfStockMarkings constructor.
     *
     * @param string $markings
     */
    public function __construct(string $markings)
    {
        $this->markings = $markings;
    }
    
    public function value(): string
    {
        return $this->markings;
    }
}