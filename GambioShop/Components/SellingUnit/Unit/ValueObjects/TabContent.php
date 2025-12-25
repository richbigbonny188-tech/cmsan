<?php
/* --------------------------------------------------------------
  TabContent.php 2020-02-18
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2020 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\SellingUnit\Unit\ValueObjects;

/**
 * Class TabContent
 * @package Gambio\Shop\SellingUnit\Unit\ValueObjects
 */
class TabContent
{
    /**
     * @var string
     */
    private $content;
    
    
    /**
     * TabContent constructor.
     *
     * @param string $content
     */
    public function __construct(string $content)
    {
        $this->content = $content;
    }
    
    
    /**
     * @return string
     */
    public function value() : string
    {
        return $this->content;
    }
}