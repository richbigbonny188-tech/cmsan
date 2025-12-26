<?php
/*------------------------------------------------------------------------------
  SummarizedData.php 2021-09-20
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2021 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  -----------------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\DashboardStatistics\Model\ValueObjects;

use Gambio\Admin\Modules\DashboardStatistics\Model\Collections\SummaryPartials;
use Gambio\Admin\Modules\DashboardStatistics\Model\Collections\SummaryTotals;
use JsonSerializable;

class SummarizedData implements JsonSerializable
{
    /**
     * @var SummaryTitle
     */
    private $title;
    
    /**
     * @var SummaryHint
     */
    private $hint;
    
    /**
     * @var SummaryTotals
     */
    private $summaryTotals;
    
    /**
     * @var SummaryPartials
     */
    private $summaryPartials;
    
    /**
     * @var SummaryPartials
     */
    private $summaryComparisonPartials;
    
    /**
     * @var SummaryName
     */
    private $name;
    
    /**
     * @var string[]
     */
    private $sortOrder = ['sales', 'orders', 'conversions', 'visitors'];
    
    
    /**
     * Constructor.
     */
    public function __construct(
        SummaryName $name,
        SummaryTitle $title,
        SummaryHint $hint,
        SummaryTotals $summaryTotals,
        SummaryPartials $summaryPartials,
        SummaryPartials $summaryComparisonPartials
    ) {
        $this->title                     = $title;
        $this->hint                      = $hint;
        $this->summaryTotals             = $summaryTotals;
        $this->summaryPartials           = $summaryPartials;
        $this->summaryComparisonPartials = $summaryComparisonPartials;
        $this->name                      = $name;
    }
    
    
    /**
     * Return name.
     */
    public function name(): SummaryName
    {
        return $this->name;
    }
    
    
    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        $totals = [];
        foreach ($this->summaryTotals as $total) {
            $totals[$total->name()] = [
                'total'  => $total->total(),
                'change' => $total->change(),
            ];
        }
        
        // adjust the sort order of the total items
        $orderedTotals = [];
        foreach ($this->sortOrder as $categoryName) {
            if (isset($totals[$categoryName])) {
                $orderedTotals[$categoryName] = $totals[$categoryName];
            }
        }
        
        $totals = array_merge($orderedTotals, $totals);
        
        $partials = [];
        foreach ($this->summaryPartials as $partial) {
            $item = ['label' => $partial->label()->label()];
            foreach ($partial->values() as $partialValue) {
                $item[$partialValue->title()] = $partialValue->value();
            }
            $partials[] = $item;
        }
        
        $comparisonPartials = [];
        foreach ($this->summaryComparisonPartials as $partial) {
            $item = ['label' => $partial->label()->label()];
            foreach ($partial->values() as $partialValue) {
                $item[$partialValue->title()] = $partialValue->value();
            }
            $comparisonPartials[] = $item;
        }
        
        return [
            'title'                => $this->title->title(),
            'periodComparisonHint' => $this->hint->hint(),
            'totals'               => $totals,
            'partials'             => $partials,
            'comparisonPartials'   => $comparisonPartials,
        ];
    }
}