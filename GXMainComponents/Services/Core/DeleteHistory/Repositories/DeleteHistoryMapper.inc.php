<?php
/* --------------------------------------------------------------
   DeleteHistoryMapper.inc.php 2018-07-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class DeleteHistoryMapper
 */
class DeleteHistoryMapper implements DeleteHistoryMapperInterface
{
    /**
     * @var \DeleteHistoryReaderInterface
     */
    protected $reader;
    
    /**
     * @var \DeleteHistoryWriterInterface
     */
    protected $writer;
    
    
    /**
     * DeleteHistoryMapper constructor.
     *
     * @param \DeleteHistoryDataAdapterInterface $dataAdapter
     */
    public function __construct(DeleteHistoryDataAdapterInterface $dataAdapter)
    {
        $this->reader = $dataAdapter->reader();
        $this->writer = $dataAdapter->writer();
    }
    
    
    /**
     * Reports deleted entities in given scope and date range.
     *
     * @param \DateRange          $range Date range for search.
     * @param \DeleteHistoryScope $scope Delete history scope for search.
     *
     * @return DeleteHistoryReport Delete history report items.
     */
    public function findDeleted(DateRange $range, DeleteHistoryScope $scope)
    {
        $reportData = $this->reader->fetch(new NonEmptyStringType($scope->scope()), $range->begin(), $range->end());
        
        $reports = [];
        
        foreach ($reportData as $report) {
            $reports[] = DeleteHistoryReportItem::create($report['deleted_id'],
                                                         $scope,
                                                         new DateTime($report['created_at']));
        }
        
        return DeleteHistoryReport::collect($reports);
    }
    
    
    /**
     * Reports an entity deletion.
     *
     * @param \DeletedId          $id    Id of deleted entity.
     * @param \DeleteHistoryScope $scope Entities scope.
     *
     * @return void
     */
    public function reportDeletion(DeletedId $id, DeleteHistoryScope $scope)
    {
        $this->writer->insert(new NonEmptyStringType($scope->scope()), new NonEmptyStringType($id->id()));
    }
}