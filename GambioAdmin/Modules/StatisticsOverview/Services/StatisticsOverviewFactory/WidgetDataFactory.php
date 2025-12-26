<?php
/*--------------------------------------------------------------
   WidgetDataFactory.php 2022-05-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\StatisticsOverview\Services\StatisticsOverviewFactory;

use Gambio\Admin\Modules\StatisticsOverview\Services\StatisticsOverviewFactory\WidgetDataFactory\MapDataFactory;
use Gambio\Admin\Modules\StatisticsOverview\Services\StatisticsOverviewFactory\WidgetDataFactory\NumberDataFactory;
use Gambio\Admin\Modules\StatisticsOverview\Services\StatisticsOverviewFactory\WidgetDataFactory\SerialDataFactory;
use Gambio\Admin\Modules\StatisticsOverview\Services\StatisticsOverviewFactory\WidgetDataFactory\TableDataFactory;
use Gambio\Admin\Modules\StatisticsOverview\Services\StatisticsOverviewFactory\WidgetDataFactory\TextDataFactory;
use Gambio\Admin\Modules\StatisticsOverview\Services\StatisticsOverviewFactory\WidgetDataFactory\TimespanFactory;

/**
 * Class representing widget data factory.
 *
 * @package Gambio\Admin\Modules\StatisticsOverview\Services\StatisticsOverviewFactory\WidgetOptionFactory
 */
class WidgetDataFactory
{
    /**
     * Map data factory.
     *
     * @var MapDataFactory
     */
    private $mapFactory;
    
    /**
     * Serial data factory.
     *
     * @var SerialDataFactory
     */
    private $serialFactory;
    
    /**
     * Table data factory.
     *
     * @var TableDataFactory
     */
    private $tableFactory;
    
    /**
     * Text data factory.
     *
     * @var TextDataFactory
     */
    private $textFactory;
    
    /**
     * Number data factory.
     *
     * @var NumberDataFactory
     */
    private $numberFactory;
    
    /**
     * Time span factory.
     *
     * @var TimespanFactory
     */
    private $timespanFactory;
    
    
    /**
     * Constructor.
     *
     * @param MapDataFactory    $mapFactory      Map data factory.
     * @param SerialDataFactory $serialFactory   Serial data factory.
     * @param TableDataFactory  $tableFactory    Table data factory.
     * @param TextDataFactory   $textFactory     Text factory.
     * @param NumberDataFactory $numberFactory   Number factory.
     * @param TimespanFactory   $timespanFactory Time span factory.
     */
    public function __construct(
        MapDataFactory $mapFactory,
        SerialDataFactory $serialFactory,
        TableDataFactory $tableFactory,
        TextDataFactory $textFactory,
        NumberDataFactory $numberFactory,
        TimespanFactory $timespanFactory
    ) {
        $this->mapFactory      = $mapFactory;
        $this->serialFactory   = $serialFactory;
        $this->tableFactory    = $tableFactory;
        $this->textFactory     = $textFactory;
        $this->numberFactory   = $numberFactory;
        $this->timespanFactory = $timespanFactory;
    }
    
    
    /**
     * Return timespan factory.
     *
     * @return TimespanFactory Time span factory.
     */
    public function useTimespan(): TimespanFactory
    {
        return $this->timespanFactory;
    }
    
    
    /**
     * Return map data factory.
     *
     * @return MapDataFactory Map data factory.
     */
    public function useMapData(): MapDataFactory
    {
        return $this->mapFactory;
    }
    
    
    /**
     * Return serial data factory.
     *
     * @return SerialDataFactory Serial data factory.
     */
    public function useSerialData(): SerialDataFactory
    {
        return $this->serialFactory;
    }
    
    
    /**
     * Return table data factory.
     *
     * @return TableDataFactory Table data factory.
     */
    public function useTableData(): TableDataFactory
    {
        return $this->tableFactory;
    }
    
    
    /**
     * Return text data factory.
     *
     * @return TextDataFactory Text data factory.
     */
    public function useTextData(): TextDataFactory
    {
        return $this->textFactory;
    }
    
    
    /**
     * Return number data factory.
     *
     * @return NumberDataFactory Number data factory.
     */
    public function useNumberData(): NumberDataFactory
    {
        return $this->numberFactory;
    }
}