<?php
/* --------------------------------------------------------------
  Tab.php 2020-02-18
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2020 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\SellingUnit\Unit\Entities;

use Gambio\Shop\SellingUnit\Unit\ValueObjects\TabContent;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\TabTitle;

/**
 * Class Tab
 * @package Gambio\Shop\SellingUnit\Unit\Entities
 */
class Tab
{
    /**
     * @var TabTitle
     */
    private $title;
    
    /**
     * @var TabContent
     */
    private $content;
    
    
    /**
     * Tab constructor.
     *
     * @param TabTitle   $title
     * @param TabContent $content
     */
    public function __construct(TabTitle $title, TabContent $content)
    {
        $this->title   = $title;
        $this->content = $content;
    }
    
    
    /**
     * @return TabTitle
     */
    public function title() : TabTitle
    {
        return $this->title;
    }
    
    
    /**
     * @return TabContent
     */
    public function content() : TabContent
    {
        return $this->content;
    }
}