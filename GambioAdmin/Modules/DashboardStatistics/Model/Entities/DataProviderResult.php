<?php
/*--------------------------------------------------------------
  DataProviderResult.php 2021-09-20
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2021 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\DashboardStatistics\Model\Entities;

use Gambio\Admin\Modules\DashboardStatistics\Model\Collections\Values;
use Gambio\Admin\Modules\DashboardStatistics\Model\ValueObjects\MinimumFractionDigits;
use Gambio\Admin\Modules\DashboardStatistics\Model\ValueObjects\Name;
use Gambio\Admin\Modules\DashboardStatistics\Model\ValueObjects\Style;
use Gambio\Admin\Modules\DashboardStatistics\Model\ValueObjects\Title;
use Gambio\Admin\Modules\DashboardStatistics\Model\ValueObjects\Unit;
use JsonSerializable;

class DataProviderResult implements JsonSerializable
{
    /**
     * @var Name
     */
    protected $name;
    
    /**
     * @var Title
     */
    protected $title;
    
    /**
     * @var Unit
     */
    protected $unit;
    
    /**
     * @var Style
     */
    protected $style;
    
    /**
     * @var MinimumFractionDigits
     */
    protected $minimumFractionDigits;
    
    /**
     * @var Values
     */
    protected $statisticValues;
    
    
    /**
     * Constructor.
     */
    public function __construct(
        Name $name,
        Title $title,
        Unit $unit,
        Style $style,
        MinimumFractionDigits $minimumFractionDigits,
        Values $statisticValues
    ) {
        $this->name                  = $name;
        $this->title                 = $title;
        $this->unit                  = $unit;
        $this->style                 = $style;
        $this->minimumFractionDigits = $minimumFractionDigits;
        $this->statisticValues       = $statisticValues;
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
    
    
    /**
     * Return values.
     */
    public function statisticValueCollection(): Values
    {
        return $this->statisticValues;
    }
    
    
    /**
     * @inheritDoc
     */
    public function jsonSerialize(): object
    {
        return (object)[
            'name'                  => $this->name(),
            'title'                 => $this->title(),
            'unit'                  => $this->unit(),
            'style'                 => $this->style(),
            'minimumFractionDigits' => $this->minimumFractionDigits(),
            'values'                => $this->statisticValueCollection(),
        ];
    }
}