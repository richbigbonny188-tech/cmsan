<?php
/* --------------------------------------------------------------
   DeleteHistoryWriteServiceInterface.inc.php 2018-07-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface DeleteHistoryWriteServiceInterface
 */
interface DeleteHistoryWriteServiceInterface
{
    /**
     * Reports the deletion of an entity with the given id in the given scope.
     *
     * @param \DeletedId          $id    Value of deleted id.
     * @param \DeleteHistoryScope $scope Scope of deleted entity.
     *
     * @return void
     */
    public function reportDeletion(DeletedId $id, DeleteHistoryScope $scope);
}
