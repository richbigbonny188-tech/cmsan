<?php
/*--------------------------------------------------------------------
 ShowWeight.php 2020-2-24
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\SellingUnit\Unit\ValueObjects;

/**
 * Class ShowWeight
 * @package Gambio\Shop\SellingUnit\Unit\ValueObjects
 */
class ShowWeight
{
    /**
     * @var bool
     */
    protected $showWeight;
    
    
    /**
     * ShowWeight constructor.
     *
     * @param bool $showWeight
     */
    public function __construct(bool $showWeight)
    {
        $this->showWeight = $showWeight;
    }
    
    
    /**
     * @return bool
     */
    public function value(): bool
    {
        return $this->showWeight;
    }
}