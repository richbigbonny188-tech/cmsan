<?php
/* --------------------------------------------------------------
   DeleteHistoryReadServiceInterface.inc.php 2018-07-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface DeleteHistoryReadServiceInterface
 */
interface DeleteHistoryReadServiceInterface
{
    /**
     * Reports about removed entities in given scope and date range.
     *
     * @param \DateRange          $range Date range for search.
     * @param \DeleteHistoryScope $scope Domain scope for search.
     *
     * @return \DeleteHistoryReport Report about removed entities.
     */
    public function findDeleted(DateRange $range, DeleteHistoryScope $scope);
}
