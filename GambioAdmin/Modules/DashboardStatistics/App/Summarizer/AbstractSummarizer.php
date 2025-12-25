<?php
/*------------------------------------------------------------------------------
 AbstractSummarizer.php 2021-09-20
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2021 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -----------------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\DashboardStatistics\App\Summarizer;

use DateInterval;
use DateTimeInterface;
use Gambio\Admin\Modules\DashboardStatistics\Model\Collections\SummaryPartials;
use Gambio\Admin\Modules\DashboardStatistics\Model\Collections\SummaryTotals;
use Gambio\Admin\Modules\DashboardStatistics\Model\Entities\DataProviderResult;
use Gambio\Admin\Modules\DashboardStatistics\Model\Entities\Timespan;
use Gambio\Admin\Modules\DashboardStatistics\Model\ValueObjects\PartialLabel;
use Gambio\Admin\Modules\DashboardStatistics\Model\ValueObjects\PartialValue;
use Gambio\Admin\Modules\DashboardStatistics\Model\ValueObjects\SummarizedData;
use Gambio\Admin\Modules\DashboardStatistics\Model\ValueObjects\SummaryHint;
use Gambio\Admin\Modules\DashboardStatistics\Model\ValueObjects\SummaryName;
use Gambio\Admin\Modules\DashboardStatistics\Model\ValueObjects\SummaryPartial;
use Gambio\Admin\Modules\DashboardStatistics\Model\ValueObjects\SummaryTitle;
use Gambio\Admin\Modules\DashboardStatistics\Model\ValueObjects\SummaryTotal;
use Gambio\Core\TextManager\Services\TextManager;

abstract class AbstractSummarizer
{
    /**
     * @var DataProviderResult[]
     */
    protected $values;
    
    /**
     * @var TextManager
     */
    protected $textManager;
    
    
    /**
     * Constructor.
     */
    public function __construct(TextManager $textManager)
    {
        $this->textManager = $textManager;
    }
    
    
    /**
     * Return summarized data array.
     */
    public function summarizeAsArray(
        string $format,
        Timespan $timespan
    ): array {
        $partials           = $this->buildPartials($format,
                                                   $timespan->startDate(),
                                                   $timespan->endDate());
        $comparisonPartials = $this->buildPartials($format,
                                                   $timespan->comparisonStartDate(),
                                                   $timespan->comparisonEndDate());
        
        $this->alignPartials($partials, $comparisonPartials);
        
        return [
            'totals'             => $this->buildTotals($format, $timespan),
            'partials'           => $partials,
            'comparisonPartials' => $comparisonPartials,
        ];
    }
    
    
    /**
     * Parse data.
     */
    protected function parseArray(
        array $struct,
        SummaryTitle $title,
        SummaryHint $hint,
        SummaryName $summaryName,
        array $translationMap
    ): SummarizedData {
        $totalsArray = [];
        foreach ($struct['totals'] as $name => $value) {
            $totalsArray[] = new SummaryTotal($name, $value['total'], $value['change']);
        }
        $totals = new SummaryTotals($totalsArray);
        
        $partialsArray = [];
        
        foreach ($struct['partials'] as $label => $partialValues) {
            $partialsValuesArray = [];
            foreach ($partialValues as $groupName => $value) {
                $partialsValuesArray[] = new PartialValue($groupName, $value);
            }
            $formattedLabel  = sprintf($translationMap[$label], $label);
            $labelInstance   = new PartialLabel($formattedLabel);
            $partialsArray[] = new SummaryPartial($labelInstance, ...$partialsValuesArray);
        }
        
        $partials = new SummaryPartials($partialsArray);
        
        $comparisonPartialsArray = [];
        
        foreach ($struct['comparisonPartials'] as $label => $partialValues) {
            $partialsValuesArray = [];
            foreach ($partialValues as $groupName => $value) {
                $partialsValuesArray[] = new PartialValue($groupName, $value);
            }
            $formattedLabel            = array_key_exists($label, $translationMap) ? sprintf($translationMap[$label],
                                                                                             $label) : '';
            $comparisonPartialsArray[] = new SummaryPartial(new PartialLabel($formattedLabel), ...
                                                            $partialsValuesArray);
        }
        
        $comparisonPartials = new SummaryPartials($comparisonPartialsArray);
        
        return new SummarizedData($summaryName, $title, $hint, $totals, $partials, $comparisonPartials);
    }
    
    
    /**
     * Initialize data.
     */
    protected function initializeEmptyStructure(
        string $format,
        DateTimeInterface $startDate,
        DateTimeInterface $endDate
    ): array {
        $groupsArray   = [];
        $currentDate   = clone $startDate;
        $partialsArray = [];
        
        foreach ($this->values as $group) {
            $groupsArray[$group->name()->value()] = 0;
        }
        ksort($groupsArray);
        
        $dateInterval = DateInterval::createFromDateString('1 day');
        while ($currentDate <= $endDate) {
            $label                 = $currentDate->format($format);
            $label                 = is_numeric($label) ? (int)$label : $label;
            $partialsArray[$label] = $groupsArray;
            $currentDate           = $currentDate->add($dateInterval);
        }
        
        return $partialsArray;
    }
    
    
    /**
     * Calculate totals.
     */
    protected function calculateTotals(
        string $format,
        DateTimeInterface $startDate,
        DateTimeInterface $endDate
    ): array {
        $totalsPartialsArray = $this->buildPartials($format, $startDate, $endDate);
        
        //create the totals
        $totalsArray = [];
        foreach ($totalsPartialsArray as $partials) {
            foreach ($partials as $groupName => $partial) {
                if (!isset($totalsArray[$groupName])) {
                    $totalsArray[$groupName] = ['total' => 0, 'change' => 0];
                }
                $totalsArray[$groupName]['total'] += $partial;
            }
        }
        
        // calculate conversion rate
        if (!empty($totalsArray['visitors']['total'])) {
            $totalsArray['conversions']['total'] = 100 / $totalsArray['visitors']['total']
                                                   * $totalsArray['orders']['total'];
            $totalsArray['conversions']['total'] = round($totalsArray['conversions']['total'], 2);
        }
        
        return $totalsArray;
    }
    
    
    /**
     * Build partials.
     */
    protected function buildPartials(
        string $format,
        DateTimeInterface $startDate,
        DateTimeInterface $endDate
    ): array {
        $partialsArray = $this->initializeEmptyStructure($format, $startDate, $endDate);
        
        foreach ($this->values as $providerResult) {
            //Initialize all the values slots
            foreach ($providerResult->statisticValueCollection() as $statisticValue) {
                
                if ($statisticValue->date()->asDateTime() >= $startDate
                    && $statisticValue->date()->asDateTime() <= $endDate) {
                    
                    $label     = $statisticValue->date()->asDateTime()->format($format);
                    $label     = is_numeric($label) ? (int)$label : $label;
                    $groupName = $providerResult->name()->value();
                    
                    $partialsArray[$label][$groupName] += $statisticValue->value();
                }
            }
        }
        
        return $partialsArray;
    }
    
    
    /**
     * Build totals.
     */
    protected function buildTotals(string $format, Timespan $timespan): array
    {
        $totalsArray = $this->calculateTotals($format,
                                              $timespan->totalsStartDate(),
                                              $timespan->totalsEndDate());
        
        $comparisonTotalsArray = $this->calculateTotals($format,
                                                        $timespan->comparisonTotalsStartDate(),
                                                        $timespan->comparisonTotalsEndDate());
        foreach ($comparisonTotalsArray as $key => $values) {
            $totalsArray[$key]['change'] = $totalsArray[$key]['total'] - $values['total'];
        }
        
        return $totalsArray;
    }
    
    
    /**
     * Align partials.
     *
     * Partials and comparison partials need to have the same number of datasets. Missing datasets are added to the
     * smaller one.
     */
    protected function alignPartials(array &$partials, array &$comparisonPartials): void
    {
        $difference = count($partials) - count($comparisonPartials);
        
        if ($difference > 0) {
            $missingPartials = array_slice($partials, $difference * -1, $difference, true);
            foreach ($missingPartials as $partial) {
                foreach ($partial as &$value) {
                    $value = 0;
                }
                $comparisonPartials[] = $partial;
            }
        } elseif ($difference < 0) {
            $missingPartials = array_slice($comparisonPartials, $difference, $difference * -1, true);
            foreach ($missingPartials as $partial) {
                foreach ($partial as &$value) {
                    $value = 0;
                }
                $partials[] = $partial;
            }
        }
    }
    
    
    /**
     * Summarize dataset for time span.
     */
    abstract public function summarize(
        Timespan $timespan,
        DataProviderResult ...$values
    ): SummarizedData;
}