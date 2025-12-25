<?php
/* --------------------------------------------------------------
   DeleteHistoryWriterInterface.inc.php 2018-07-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface DeleteHistoryWriterInterface
 */
interface DeleteHistoryWriterInterface
{
    /**
     * Adds a new delete history record in the storage.
     *
     * @param \NonEmptyStringType $scope     Delete history scope key.
     * @param \NonEmptyStringType $deletedId Deleted id.
     *
     * @return void
     */
    public function insert(NonEmptyStringType $scope, NonEmptyStringType $deletedId);
}