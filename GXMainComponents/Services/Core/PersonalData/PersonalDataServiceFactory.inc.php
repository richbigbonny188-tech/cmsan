<?php
/* --------------------------------------------------------------
   PersonalDataServiceFactory.inc.php 2018-05-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class PersonalDataServiceFactory
 */
class PersonalDataServiceFactory implements PersonalDataServiceFactoryInterface
{
    /**
     * @var OrderReadServiceInterface
     */
    protected $orderReadService;
    
    /**
     * @var OrderWriteServiceInterface
     */
    protected $orderWriteService;
    
    /**
     * @var CustomerReadServiceInterface
     */
    protected $customerReadService;
    
    /**
     * @var CustomerServiceInterface
     */
    protected $customerService;
    
    /**
     * @var EmailServiceInterface
     */
    protected $emailService;
    
    /**
     * @var WithdrawalReadServiceInterface
     */
    protected $withdrawalReadService;
    
    /**
     * @var WithdrawalWriteServiceInterface
     */
    protected $withdrawalWriteService;
    
    /**
     * @var ReviewReadServiceInterface
     */
    protected $reviewReadService;
    
    /**
     * @var ReviewWriteServiceInterface
     */
    protected $reviewWriteService;
    
    /**
     * @var ShoppingCartServiceInterface
     */
    protected $shoppingCartService;
    
    /**
     * @var SharedShoppingCartServiceInterface
     */
    protected $sharedShoppingCartService;
    
    /**
     * @var NewsletterSubscriptionServiceInterface
     */
    protected $newsletterSubscriptionService;
    
    /**
     * @var AddressBookServiceInterface
     */
    protected $addressBookService;
    
    /**
     * @var InvoiceArchiveReadServiceInterface
     */
    protected $invoiceArchiveReadService;
    
    /**
     * @var InvoiceArchiveWriteServiceInterface
     */
    protected $invoiceArchiveWriteService;
    
    /**
     * @var PackingSlipServiceInterface
     */
    protected $packingSlipService;
    
    /**
     * @var AgreementWriteServiceInterface
     */
    protected $agreementWriteService;
    
    /**
     * @var AgreementReadServiceInterface
     */
    protected $agreementReadService;
    
    /**
     * @var PersonalDataInvoiceFileStorageInterface
     */
    protected $invoiceFileStorage;
    
    /**
     * @var PersonalDataPackingSlipFileStorageInterface
     */
    protected $packingSlipFileStorage;
    
    /**
     * @var PersonalDataXmlSerializer
     */
    protected $xmlSerializer;
    
    
    /**
     * PersonalDataServiceFactory constructor.
     *
     * @param \OrderReadServiceInterface              $orderReadService
     * @param \OrderWriteServiceInterface             $orderWriteService
     * @param \CustomerReadServiceInterface           $customerReadService
     * @param \CustomerServiceInterface               $customerService
     * @param \EmailServiceInterface                  $emailService
     * @param \WithdrawalReadServiceInterface         $withdrawalReadService
     * @param \WithdrawalWriteServiceInterface        $withdrawalWriteService
     * @param \ReviewReadServiceInterface             $reviewReadService
     * @param \ReviewWriteServiceInterface            $reviewWriteService
     * @param \ShoppingCartServiceInterface           $shoppingCartService
     * @param \SharedShoppingCartServiceInterface     $sharedShoppingCartService
     * @param \NewsletterSubscriptionServiceInterface $newsletterSubscriptionService
     * @param \AddressBookServiceInterface            $addressBookService
     * @param \InvoiceArchiveReadServiceInterface     $invoiceArchiveReadService
     * @param \InvoiceArchiveWriteServiceInterface    $invoiceArchiveWriteService
     * @param \PackingSlipServiceInterface            $packingSlipService
     * @param \AgreementWriteServiceInterface         $agreementWriteService
     * @param \AgreementReadServiceInterface          $agreementReadService
     * @param \PersonalDataXmlSerializer              $xmlSerializer
     */
    public function __construct(
        OrderReadServiceInterface $orderReadService,
        OrderWriteServiceInterface $orderWriteService,
        CustomerReadServiceInterface $customerReadService,
        CustomerServiceInterface $customerService,
        EmailServiceInterface $emailService,
        WithdrawalReadServiceInterface $withdrawalReadService,
        WithdrawalWriteServiceInterface $withdrawalWriteService,
        ReviewReadServiceInterface $reviewReadService,
        ReviewWriteServiceInterface $reviewWriteService,
        ShoppingCartServiceInterface $shoppingCartService,
        SharedShoppingCartServiceInterface $sharedShoppingCartService,
        NewsletterSubscriptionServiceInterface $newsletterSubscriptionService,
        AddressBookServiceInterface $addressBookService,
        InvoiceArchiveReadServiceInterface $invoiceArchiveReadService,
        InvoiceArchiveWriteServiceInterface $invoiceArchiveWriteService,
        PackingSlipServiceInterface $packingSlipService,
        AgreementWriteServiceInterface $agreementWriteService,
        AgreementReadServiceInterface $agreementReadService,
        PersonalDataXmlSerializer $xmlSerializer
    ) {
        $this->orderReadService              = $orderReadService;
        $this->orderWriteService             = $orderWriteService;
        $this->customerReadService           = $customerReadService;
        $this->customerService               = $customerService;
        $this->emailService                  = $emailService;
        $this->withdrawalReadService         = $withdrawalReadService;
        $this->withdrawalWriteService        = $withdrawalWriteService;
        $this->reviewReadService             = $reviewReadService;
        $this->reviewWriteService            = $reviewWriteService;
        $this->shoppingCartService           = $shoppingCartService;
        $this->sharedShoppingCartService     = $sharedShoppingCartService;
        $this->newsletterSubscriptionService = $newsletterSubscriptionService;
        $this->addressBookService            = $addressBookService;
        $this->invoiceArchiveReadService     = $invoiceArchiveReadService;
        $this->invoiceArchiveWriteService    = $invoiceArchiveWriteService;
        $this->packingSlipService            = $packingSlipService;
        $this->agreementWriteService         = $agreementWriteService;
        $this->agreementReadService          = $agreementReadService;
        $this->xmlSerializer                 = $xmlSerializer;
    }
    
    
    /**
     * Creates a PersonalDataService instance.
     *
     * @return PersonalDataServiceInterface
     */
    public function createService()
    {
        $service = MainFactory::create(PersonalDataService::class,
                                       $this->orderReadService,
                                       $this->orderWriteService,
                                       $this->customerReadService,
                                       $this->customerService,
                                       $this->emailService,
                                       $this->withdrawalReadService,
                                       $this->withdrawalWriteService,
                                       $this->reviewReadService,
                                       $this->reviewWriteService,
                                       $this->shoppingCartService,
                                       $this->sharedShoppingCartService,
                                       $this->newsletterSubscriptionService,
                                       $this->addressBookService,
                                       $this->agreementWriteService,
                                       $this->agreementReadService,
                                       $this->createInvoiceFileStorage(),
                                       $this->createPackingSlipFileStorage(),
                                       $this->createServiceSettings(),
                                       $this->xmlSerializer);
        
        if (gm_get_conf('GM_PDF_INVOICE_USE_CURRENT_DATE') !== null) {
            $service->setInvoiceArchiveReadService($this->invoiceArchiveReadService);
            $service->setInvoiceArchiveWriteService($this->invoiceArchiveWriteService);
            $service->setPackingSlipService($this->packingSlipService);
        }
        
        return $service;
    }
    
    
    /**
     * Creates an invoice file storage.
     *
     * @return bool|\PersonalDataInvoiceFileStorage
     * @throws InvalidArgumentException
     *
     */
    protected function createInvoiceFileStorage()
    {
        $invoiceServiceSettings = $this->createInvoiceServiceSettings();
        
        return MainFactory::create(PersonalDataInvoiceFileStorage::class,
                                   new WritableDirectory($invoiceServiceSettings->getInvoicesDirPath()));
    }
    
    
    /**
     * Creates invoice service settings.
     *
     * @return bool|\EnvInvoiceServiceSettings
     */
    protected function createInvoiceServiceSettings()
    {
        return MainFactory::create(EnvInvoiceServiceSettings::class);
    }
    
    
    /**
     * Creates a packing slip file storage.
     *
     * @return bool|\PersonalDataPackingSlipFileStorage
     * @throws InvalidArgumentException
     *
     */
    protected function createPackingSlipFileStorage()
    {
        $packingSlipServiceSettings = $this->createPackingSlipServiceSettings();
        
        return MainFactory::create(PersonalDataPackingSlipFileStorage::class,
                                   new WritableDirectory($packingSlipServiceSettings->getPackingSlipDirPath()));
    }
    
    
    /**
     * Creates packing slip service settings.
     *
     * @return bool|\EnvPackingSlipServiceSettings
     */
    protected function createPackingSlipServiceSettings()
    {
        return MainFactory::create(EnvPackingSlipServiceSettings::class);
    }
    
    
    /**
     * Creates personal data service settings.
     *
     * @return bool|\PersonalDataServiceSettings
     */
    protected function createServiceSettings()
    {
        return MainFactory::create(PersonalDataServiceSettings::class);
    }
}