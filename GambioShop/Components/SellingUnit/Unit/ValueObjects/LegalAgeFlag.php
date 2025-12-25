<?php
/* --------------------------------------------------------------
  LegalAgeFlag.php 2020-02-19
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2020 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\SellingUnit\Unit\ValueObjects;

/**
 * Class LegalAgeFlag
 * @package Gambio\Shop\SellingUnit\Unit\ValueObjects
 */
class LegalAgeFlag
{
    /**
     * @var bool
     */
    protected $flag;
    
    
    /**
     * LegalAgeFlag constructor.
     *
     * @param bool $flag
     */
    public function __construct(bool $flag)
    {
        $this->flag = $flag;
    }
    
    
    /**
     * @return bool
     */
    public function value() : bool
    {
        return $this->flag;
    }
    
    
}