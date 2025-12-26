<?php
/* --------------------------------------------------------------
  Status.php 2020-02-19
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2020 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\SellingUnit\Unit\ValueObjects;

/**
 * Class Status
 * @package Gambio\Shop\SellingUnit\Unit\ValueObjects
 */
class ProductStatus
{
    /**
     * @var bool
     */
    protected $status;
    
    
    /**
     * ProductStatus constructor.
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
    public function value() : bool
    {
        return $this->status;
    }
    
}