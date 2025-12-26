<?php
/*------------------------------------------------------------------------------
 Category.php 2021-09-20
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2021 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -----------------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\DashboardStatistics\Model\ValueObjects;

class Category
{
    /**
     * @var Name
     */
    private $name;
    
    /**
     * @var Title
     */
    private $title;
    
    /**
     * @var Unit
     */
    private $unit;
    
    /**
     * @var Style
     */
    private $style;
    
    /**
     * @var MinimumFractionDigits
     */
    private $minimumFractionDigits;
    
    
    /**
     * Constructor.
     */
    public function __construct(
        Name $name,
        Title $title,
        Unit $unit,
        Style $style,
        MinimumFractionDigits $minimumFractionDigits
    ) {
        $this->name                  = $name;
        $this->title                 = $title;
        $this->unit                  = $unit;
        $this->style                 = $style;
        $this->minimumFractionDigits = $minimumFractionDigits;
    }
    
    
    /**
     * Return name.
     */
    public function name(): Name
    {
        return $this->name;
    }
    
    
    /**
     * Return title.
     */
    public function title(): Title
    {
        return $this->title;
    }
    
    
    /**
     * Return unit.
     */
    public function unit(): Unit
    {
        return $this->unit;
    }
    
    
    /**
     * Return style.
     */
    public function style(): Style
    {
        return $this->style;
    }
    
    
    /**
     * Return minimum fraction digits.
     */
    public function minimumFractionDigits(): MinimumFractionDigits
    {
        return $this->minimumFractionDigits;
    }
}