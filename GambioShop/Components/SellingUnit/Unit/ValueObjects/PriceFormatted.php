<?php
/*--------------------------------------------------------------------
 PriceFormatted.php 2020-2-17
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\SellingUnit\Unit\ValueObjects;

/**
 * Class PriceFormatted
 * @package Gambio\Shop\SellingUnit\Unit\ValueObjects
 */
class PriceFormatted
{
    /**
     * @var string
     */
    protected $formatted;
    
    
    /**
     * PriceFormatted constructor.
     *
     * @param string $formatted
     */
    public function __construct(string $formatted)
    {
        $this->formatted = $formatted;
    }
    
    
    /**
     * @return string
     */
    public function value(): string
    {
        return $this->formatted;
    }
}