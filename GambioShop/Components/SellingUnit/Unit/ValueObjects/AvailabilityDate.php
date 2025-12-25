<?php
/*--------------------------------------------------------------------
 AvailabilityDate.php 2020-2-25
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\SellingUnit\Unit\ValueObjects;

/**
 * Class AvailabilityDate
 * @package Gambio\Shop\SellingUnit\Unit\ValueObjects
 */
class AvailabilityDate
{
    /**
     * @var string|null
     */
    protected $availabilityDate;
    
    
    /**
     * AvailabilityDate constructor.
     *
     * @param string|null $availabilityDate
     */
    public function __construct(?string $availabilityDate)
    {
        $this->availabilityDate = $availabilityDate;
    }
    
    
    /**
     * @return string|null
     */
    public function value() : ?string
    {
        return $this->availabilityDate;
    }
    
}