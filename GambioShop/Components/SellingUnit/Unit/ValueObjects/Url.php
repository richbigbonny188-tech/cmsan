<?php
/*--------------------------------------------------------------------
 Url.php 2020-2-18
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\SellingUnit\Unit\ValueObjects;

/**
 * Class Url
 * @package Gambio\Shop\SellingUnit\Unit\ValueObjects
 */
class Url
{
    /**
     * @var null|string
     */
    protected $url;
    
    
    /**
     * Url constructor.
     *
     * @param null|string $url
     */
    public function __construct(?string $url)
    {
        $this->url = $url;
    }
    
    
    /**
     * @return null|string
     */
    public function value(): ?string
    {
        return $this->url;
    }
}