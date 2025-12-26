<?php
/* --------------------------------------------------------------
   DeleteHistoryMapperInterface.inc.php 2018-07-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface DeleteHistoryMapperInterface
 */
interface DeleteHistoryMapperInterface
{
    /**
     * Reports deleted entities in given scope and date range.
     *
     * @param \DateRange          $range Date range for search.
     * @param \DeleteHistoryScope $scope Delete history scope for search.
     *
     * @return DeleteHistoryReport Delete history report items.
     */
    public function findDeleted(DateRange $range, DeleteHistoryScope $scope);
    
    
    /**
     * Reports an entity deletion.
     *
     * @param \DeletedId          $id    Id of deleted entity.
     * @param \DeleteHistoryScope $scope Entities scope.
     *
     * @return void
     */
    public function reportDeletion(DeletedId $id, DeleteHistoryScope $scope);
}