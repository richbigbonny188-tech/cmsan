<?php
/*--------------------------------------------------------------------
 ProductLink.php 2020-3-13
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\SellingUnit\Presentation\ValueObjects;

/**
 * Class ProductLink
 * @package Gambio\Shop\SellingUnit\Presentation\ValueObjects
 */
class ProductLink
{
    /**
     * @var string
     */
    protected $link;
    
    
    /**
     * ProductLink constructor.
     *
     * @param string $link
     */
    public function __construct(string $link)
    {
        $this->link = $link;
    }
    
    
    /**
     * @return string
     */
    public function value(): string
    {
        return $this->link;
    }
}