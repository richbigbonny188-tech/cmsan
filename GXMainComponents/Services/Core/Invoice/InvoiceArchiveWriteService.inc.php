<?php

/* --------------------------------------------------------------
   InvoiceArchiveWriteService.inc.php 2016-10-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class InvoiceArchiveWriteService
 *
 * @category   System
 * @package    Invoice
 */
class InvoiceArchiveWriteService implements InvoiceArchiveWriteServiceInterface
{
    /**
     * @var InvoiceListGeneratorInterface
     */
    protected $invoiceListGenerator;
    
    /**
     * @var InvoiceRepositoryInterface
     */
    protected $invoiceRepository;
    
    /**
     * @var InvoiceFileEntitlerInterface
     */
    protected $fileEntitler;
    
    /**
     * @var AbstractFileStorage
     */
    protected $fileStorage;
    
    
    /**
     * InvoiceListArchiveWriteService constructor.
     *
     * @param InvoiceListGeneratorInterface $invoiceListGenerator
     * @param InvoiceRepositoryInterface    $invoiceRepository
     * @param InvoiceFileEntitlerInterface  $fileEntitler
     * @param AbstractFileStorage           $fileStorage
     */
    public function __construct(
        InvoiceListGeneratorInterface $invoiceListGenerator,
        InvoiceRepositoryInterface $invoiceRepository,
        InvoiceFileEntitlerInterface $fileEntitler,
        AbstractFileStorage $fileStorage
    ) {
        $this->invoiceListGenerator = $invoiceListGenerator;
        $this->invoiceRepository    = $invoiceRepository;
        $this->fileEntitler         = $fileEntitler;
        $this->fileStorage          = $fileStorage;
    }
    
    
    /**
     * Imports the given invoice file and store their information in the database.
     *
     * @param ExistingFile       $invoiceFile Name of pdf invoice file.
     * @param InvoiceInformation $invoiceInfo Value objects which holds the invoice information
     *
     * @return int Invoice id.
     */
    public function importInvoiceFile(ExistingFile $invoiceFile, InvoiceInformation $invoiceInfo)
    {
        $invoiceId = $this->invoiceRepository->add($invoiceInfo);
        $invoiceId = new IdType($invoiceId);
        
        $invoiceFilename = $this->fileEntitler->createFilenameFromInvoiceId($invoiceId);
        $invoiceFilename = new FilenameStringType($invoiceFilename);
        
        $this->fileStorage->importFile($invoiceFile, $invoiceFilename);
        $this->invoiceRepository->updateInvoiceFilename($invoiceId, $invoiceFilename);
        
        return $invoiceId->asInt();
    }
    
    
    /**
     * Removes an invoice from the database by the given invoice id.
     *
     * @param IdType $invoiceId Id of invoice entry to be removed.
     *
     * @return $this|InvoiceArchiveWriteServiceInterface
     * @throws UnexpectedValueException if invoice does not exist
     *
     */
    public function deleteInvoiceById(IdType $invoiceId)
    {
        $itemCollection = $this->invoiceListGenerator->getInvoiceListByConditions(['invoice_id' => $invoiceId->asInt()]);
        
        if ($itemCollection->isEmpty()) {
            throw new UnexpectedValueException('The requested invoice was not found in database (ID:'
                                               . $invoiceId->asInt() . ')');
        }
        
        /** @var InvoiceListItem $invoiceListItem */
        $invoiceListItem = $itemCollection->getItem(0);
        $this->fileStorage->deleteFile(new FilenameStringType($invoiceListItem->getInvoiceFilename()));
        
        $this->invoiceRepository->deleteByInvoiceId($invoiceId);
        
        return $this;
    }
}