<?php
/* --------------------------------------------------------------
   PersonalDataInvoiceFileStorage.inc.php 2018-05-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class PersonalDataInvoiceFileStorage
 *
 * @category   System
 * @package    PersonalData
 * @subpackage Storage
 */
class PersonalDataInvoiceFileStorage extends DocumentFileStorage implements PersonalDataInvoiceFileStorageInterface
{
    /**
     * @param \InvoiceListItemCollection $invoiceList
     *
     * @return \ExistingFileCollection
     * @throws InvalidArgumentException
     *
     */
    public function getFileListByInvoiceList(InvoiceListItemCollection $invoiceList)
    {
        $invoiceFiles = [];
        
        /**
         * @var InvoiceListItem $invoice
         */
        foreach ($invoiceList as $invoice) {
            $invoiceFiles[] = new ExistingFile(new NonEmptyStringType($this->storageDirectory->getDirPath()
                                                                      . DIRECTORY_SEPARATOR
                                                                      . $invoice->getInvoiceFilename()));
        }
        
        return new ExistingFileCollection($invoiceFiles);
    }
}