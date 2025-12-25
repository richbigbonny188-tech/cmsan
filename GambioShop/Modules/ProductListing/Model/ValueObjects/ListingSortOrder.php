<?php
/*
 * --------------------------------------------------------------
 *   ListingSortOrder.php 2022-01-11
 *   Gambio GmbH
 *   http://www.gambio.de
 *   Copyright (c) 2022 Gambio GmbH
 *   Released under the GNU General Public License (Version 2)
 *   [http://www.gnu.org/licenses/gpl-2.0.html]
 * --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Shop\Modules\ProductListing\Model\ValueObjects;

/**
 * Class ListingSortOrder
 *
 * @package Gambio\Shop\Modules\ProductListing\Model\ValueObjects
 */
class ListingSortOrder
{
    private ListingSortValue $value;
    
    private ListingSortDirection $sortDirection;
    
    
    /**
     * @param ListingSortValue     $value
     * @param ListingSortDirection $sortDirection
     */
    public function __construct(ListingSortValue $value, ListingSortDirection $sortDirection)
    {
        $this->value         = $value;
        $this->sortDirection = $sortDirection;
    }
    
    
    /**
     * @return string
     */
    public function value(): string
    {
        return $this->value->value();
    }
    
    
    /**
     * @return string
     */
    public function sortDirection(): string
    {
        return $this->sortDirection->direction();
    }
}