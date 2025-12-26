<?php
/* --------------------------------------------------------------
   DeleteHistoryReaderInterface.inc.php 2018-07-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface DeleteHistoryReaderInterface
 */
interface DeleteHistoryReaderInterface
{
    /**
     * Fetches delete history data from the storage.
     *
     * @param \NonEmptyStringType $scope Delete history scope key.
     * @param \DateTime           $begin Minimum date of entry.
     * @param \DateTime           $end   Maximum date of entry.
     *
     * @return array Delete history report data.
     */
    public function fetch(NonEmptyStringType $scope, DateTime $begin, DateTime $end);
}