<?php
/* --------------------------------------------------------------
   PersonalDataPackingSlipFileStorage.inc.php 2018-05-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class PersonalDataPackingSlipFileStorage
 *
 * @category   System
 * @package    PersonalData
 * @subpackage Storage
 */
class PersonalDataPackingSlipFileStorage extends DocumentFileStorage
    implements PersonalDataPackingSlipFileStorageInterface
{
    /**
     * @param \PackingSlipCollection $packingSlipList
     *
     * @return \ExistingFileCollection
     * @throws InvalidArgumentException
     *
     */
    public function getFileListByPackingSlipList(PackingSlipCollection $packingSlipList)
    {
        $packingSlipFiles = [];
        
        /**
         * @var PackingSlip $packingSlip
         */
        foreach ($packingSlipList as $packingSlip) {
            $packingSlipFiles[] = new ExistingFile(new NonEmptyStringType($this->storageDirectory->getDirPath()
                                                                          . DIRECTORY_SEPARATOR
                                                                          . $packingSlip->getFilename()));
        }
        
        return new ExistingFileCollection($packingSlipFiles);
    }
}