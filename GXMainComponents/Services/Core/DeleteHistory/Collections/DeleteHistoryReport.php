<?php
/* --------------------------------------------------------------
   DeleteHistoryReport.php 2023-03-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class DeleteHistoryReport
 */
class DeleteHistoryReport implements IteratorAggregate
{
    /**
     * @var \DeleteHistoryReportItem[]
     */
    protected $reports = [];
    
    
    /**
     * DeleteHistoryReport constructor.
     *
     * @param \DeleteHistoryReportItem[] $reports Delete history reports.
     */
    public function __construct(array $reports)
    {
        foreach ($reports as $report) {
            $this->_add($report);
        }
    }
    
    
    /**
     * Named constructor of delete history report.
     *
     * @param \DeleteHistoryReportItem[] $reports Delete history reports.
     *
     * @return \DeleteHistoryReport New instance.
     */
    public static function collect(array $reports)
    {
        return MainFactory::create(static::class, $reports);
    }
    
    
    /**
     * Retrieve an external iterator
     * @link  http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     * @since 5.0.0
     */
    public function getIterator(): \Traversable
    {
        return new ArrayIterator($this->reports);
    }
    
    
    /**
     * Adds an report item to the internal reports list.
     *
     * @param \DeleteHistoryReportItem $report New report item.
     */
    protected function _add(DeleteHistoryReportItem $report)
    {
        $this->reports[] = $report;
    }
}
