<?php
/* --------------------------------------------------------------
   InvoiceServiceFactory.inc.php 2016-10-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class InvoiceServiceFactory
 *
 * @category   System
 * @package    Invoice
 */
class InvoiceServiceFactory extends AbstractInvoiceServiceFactory
{
    /**
     * @var CI_DB_query_builder
     */
    protected $db;
    
    /**
     * @var \PaymentTitleProvider
     */
    protected $paymentTitleProvider;
    
    
    /**
     * InvoiceServiceFactory constructor.
     *
     * @param CI_DB_query_builder $db
     */
    public function __construct(CI_DB_query_builder $db)
    {
        $this->db = $db;
    }
    
    
    /**
     * Creates and returns a new invoice-archive write service instance.
     *
     * @return InvoiceArchiveWriteServiceInterface
     */
    public function createInvoiceArchiveWriteService()
    {
        $envInvoiceServiceSettings = MainFactory::create('EnvInvoiceServiceSettings');
        $invoiceListGenerator      = MainFactory::create('InvoiceListGenerator',
                                                         $this->db,
                                                         $this->_getPaymentTitleProvider());
        $invoiceStorage            = MainFactory::create('InvoiceRepository', $this->db);
        $fileEntitler              = MainFactory::create('InvoiceFileEntitler');
        $fileStorage               = MainFactory::create('DocumentFileStorage',
                                                         MainFactory::create('WritableDirectory',
                                                                             $envInvoiceServiceSettings->getInvoicesDirPath()));
        
        return MainFactory::create('InvoiceArchiveWriteService',
                                   $invoiceListGenerator,
                                   $invoiceStorage,
                                   $fileEntitler,
                                   $fileStorage);
    }
    
    
    /**
     * Creates and returns a new invoice-archive read service instance.
     *
     * @return InvoiceArchiveReadServiceInterface
     */
    public function createInvoiceArchiveReadService()
    {
        $envInvoiceServiceSettings = MainFactory::create('EnvInvoiceServiceSettings');
        $invoiceListGenerator      = MainFactory::create('InvoiceListGenerator',
                                                         $this->db,
                                                         $this->_getPaymentTitleProvider());
        
        return MainFactory::create('InvoiceArchiveReadService', $invoiceListGenerator, $envInvoiceServiceSettings);
    }
    
    
    /**
     * Creates, in memory caches and returns the payment title provider.
     *
     * @return \PaymentTitleProvider
     */
    protected function _getPaymentTitleProvider()
    {
        if (null === $this->paymentTitleProvider) {
            $this->paymentTitleProvider = MainFactory::create('PaymentTitleProvider');
        }
        
        return $this->paymentTitleProvider;
    }
}