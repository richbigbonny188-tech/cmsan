<?php
/* --------------------------------------------------------------
   PersonalDataPackingSlipFileStorageInterface.inc.php 2018-05-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface PersonalDataPackingSlipFileStorageInterface
 */
interface PersonalDataPackingSlipFileStorageInterface
{
    /**
     * @param \PackingSlipCollection $packingSlipList
     *
     * @return \ExistingFileCollection
     * @throws InvalidArgumentException
     *
     */
    public function getFileListByPackingSlipList(PackingSlipCollection $packingSlipList);
}