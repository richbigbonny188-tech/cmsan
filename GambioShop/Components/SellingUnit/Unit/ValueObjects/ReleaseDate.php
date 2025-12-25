<?php
/*--------------------------------------------------------------------
 ReleaseDate.php 2020-2-25
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\SellingUnit\Unit\ValueObjects;

/**
 * Class ReleaseDate
 * @package Gambio\Shop\SellingUnit\Unit\ValueObjects
 */
class ReleaseDate
{
    /**
     * @var string
     */
    protected $releaseDate;
    /**
     * @var bool
     */
    protected $show;


    /**
     * ReleaseDate constructor.
     *
     * @param string $releaseDate
     * @param bool $show
     */
    public function __construct(string $releaseDate, bool $show)
    {
        $this->releaseDate = $releaseDate;
        $this->show = $show;
    }

    /**
     * @return bool
     */
    public function show() : bool
    {
        return $this->show;
    }


    /**
     * @return string
     */
    public function value() : string
    {
        return $this->releaseDate;
    }
    
}