<?php
/* --------------------------------------------------------------
  TabTitle.php 2020-02-18
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2020 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\SellingUnit\Unit\ValueObjects;

/**
 * Class TabTitle
 * @package Gambio\Shop\SellingUnit\Unit\ValueObjects
 */
class TabTitle
{
    /**
     * @var string
     */
    private $title;
    
    
    /**
     * TabTitle constructor.
     *
     * @param string $title
     */
    public function __construct(string $title)
    {
        $this->title = $title;
    }
    
    
    /**
     * @return string
     */
    public function value() : string
    {
        return $this->title;
    }
    
}