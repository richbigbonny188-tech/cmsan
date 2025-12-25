<?php
/*--------------------------------------------------------------------
 Name.php 2020-04-28
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\SellingUnit\Unit\ValueObjects;

/**
 * Class Name
 * @package Gambio\Shop\SellingUnit\Unit\ValueObjects
 */
class Name
{
    /**
     * @var string|null
     */
    protected $name;
    
    
    /**
     * Name constructor.
     *
     * @param string|null $name
     */
    public function __construct(?string $name)
    {
        $this->name = $name;
    }
    
    
    /**
     * @return string|null
     */
    public function value(): ?string
    {
        return $this->name;
    }
}