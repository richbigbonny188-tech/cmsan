<?php
/* --------------------------------------------------------------
  ProductEan.php 2020-02-20
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2020 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\SellingUnit\Unit\ValueObjects;

/**
 * Class ProductEan
 * @package Gambio\Shop\SellingUnit\Unit\ValueObjects
 */
class Ean
{
    /**
     * @var string|null
     */
    protected $ean;
    
    
    /**
     * Ean constructor.
     *
     * @param string|null $ean
     */
    public function __construct(?string $ean)
    {
        $this->ean = $ean;
    }
    
    
    /**
     * @return string|null
     */
    public function value() : ?string
    {
        return $this->ean;
    }
    
}