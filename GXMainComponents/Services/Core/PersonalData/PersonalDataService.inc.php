<?php
/* --------------------------------------------------------------
   PersonalDataService.inc.php 2023-03-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

use Gambio\Admin\Modules\Customer\Model\Events\CustomerDeleted;
use Gambio\Admin\Modules\Customer\Services\CustomerFactory;
use Gambio\Admin\Modules\Withdrawal\Model\Events\WithdrawalDeleted;
use Gambio\Admin\Modules\Withdrawal\Services\WithdrawalFactory;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * Class PersonalDataService
 */
class PersonalDataService implements PersonalDataServiceInterface
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
     * @var CustomerReadServiceInterface
     */
    protected $customerReadService;
    
    /**
     * @var CustomerService
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
     * @var AddressBookService
     */
    protected $addressBookService;
    
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
     * @var PersonalDataServiceSettingsInterface
     */
    protected $serviceSettings;
    
    /**
     * @var PersonalDataXmlSerializer
     */
    protected $xmlSerializer;
    
    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;
    
    /**
     * @var CustomerFactory
     */
    protected $customerFactory;
    
    /**
     * @var WithdrawalFactory
     */
    protected $withdrawalFactory;
    
    
    /**
     * PersonalDataService constructor.
     *
     * @param \OrderReadServiceInterface                   $orderReadService
     * @param \OrderWriteServiceInterface                  $orderWriteService
     * @param \CustomerReadService                         $customerReadService
     * @param \CustomerService                             $customerService
     * @param \EmailServiceInterface                       $emailService
     * @param \WithdrawalReadServiceInterface              $withdrawalReadService
     * @param \WithdrawalWriteServiceInterface             $withdrawalWriteService
     * @param \ReviewReadServiceInterface                  $reviewReadService
     * @param \ReviewWriteServiceInterface                 $reviewWriteService
     * @param \ShoppingCartServiceInterface                $shoppingCartService
     * @param \SharedShoppingCartServiceInterface          $sharedShoppingCartService
     * @param \NewsletterSubscriptionServiceInterface      $newsletterSubscriptionService
     * @param \AddressBookService                          $addressBookService
     * @param \AgreementWriteServiceInterface              $agreementWriteService
     * @param \AgreementReadServiceInterface               $agreementReadService
     * @param \PersonalDataInvoiceFileStorageInterface     $invoiceFileStorage
     * @param \PersonalDataPackingSlipFileStorageInterface $packingSlipFileStorage
     * @param \PersonalDataServiceSettingsInterface        $serviceSettings
     * @param \PersonalDataXmlSerializer                   $xmlSerializer
     */
    public function __construct(
        OrderReadServiceInterface $orderReadService,
        OrderWriteServiceInterface $orderWriteService,
        CustomerReadService $customerReadService,
        CustomerService $customerService,
        EmailServiceInterface $emailService,
        WithdrawalReadServiceInterface $withdrawalReadService,
        WithdrawalWriteServiceInterface $withdrawalWriteService,
        ReviewReadServiceInterface $reviewReadService,
        ReviewWriteServiceInterface $reviewWriteService,
        ShoppingCartServiceInterface $shoppingCartService,
        SharedShoppingCartServiceInterface $sharedShoppingCartService,
        NewsletterSubscriptionServiceInterface $newsletterSubscriptionService,
        AddressBookService $addressBookService,
        AgreementWriteServiceInterface $agreementWriteService,
        AgreementReadServiceInterface $agreementReadService,
        PersonalDataInvoiceFileStorageInterface $invoiceFileStorage,
        PersonalDataPackingSlipFileStorageInterface $packingSlipFileStorage,
        PersonalDataServiceSettingsInterface $serviceSettings,
        PersonalDataXmlSerializer            $xmlSerializer
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
        $this->agreementWriteService         = $agreementWriteService;
        $this->agreementReadService          = $agreementReadService;
        $this->invoiceFileStorage            = $invoiceFileStorage;
        $this->packingSlipFileStorage        = $packingSlipFileStorage;
        $this->serviceSettings               = $serviceSettings;
        $this->xmlSerializer                 = $xmlSerializer;
    
        $ldc                     = LegacyDependencyContainer::getInstance();
        $this->eventDispatcher   = $ldc->get(EventDispatcherInterface::class);
        $this->customerFactory   = $ldc->get(CustomerFactory::class);
        $this->withdrawalFactory = $ldc->get(WithdrawalFactory::class);
    }
    
    
    /**
     * Deletes all data for a given customer. The following personal data will be deleted:
     *
     * - base data
     * - addresses
     * - orders
     * - invoices
     * - packing slips
     * - withdrawals
     * - agreements
     * - emails
     * - shopping carts
     * - shared shopping carts
     * - reviews
     * - newsletter subscriptions
     *
     * @param IdType                $customerId Customer ID
     * @param PersonalDataSelection $selection  Personal data selection
     */
    public function deletePersonalDataByCustomerId(IdType $customerId, PersonalDataSelection $selection)
    {
        $customer  = $this->getCustomerById($customerId);
        $orderList = $this->getOrderListByCustomerId($customerId);
        
        if ($selection->isSelected(PersonalDataSelectionItem::ORDERS)) {
            $this->deleteInvoicesByCustomerId($customerId);
            $this->deletePackingSlipsByOrderList($orderList);
        }
        
        if ($selection->isSelected(PersonalDataSelectionItem::EMAILS)) {
            $this->deleteEmailsByCustomerEmailAddress($customer->getEmail());
        }
        
        if ($selection->isSelected(PersonalDataSelectionItem::WITHDRAWALS)) {
            $this->deleteWithdrawalsByOrderList($orderList);
        }
        
        if ($selection->isSelected(PersonalDataSelectionItem::REVIEWS)) {
            $this->deleteReviewsByCustomerId($customerId);
        }
        
        if ($selection->isSelected(PersonalDataSelectionItem::CARTS)) {
            $this->deleteSharedShoppingCartsByCustomerId($customerId);
            $this->deleteShoppingCartsByCustomerId($customerId);
        }
        
        if ($selection->isSelected(PersonalDataSelectionItem::NEWSLETTER_SUBSCRIPTIONS)) {
            $this->deleteNewsletterSubscriptionByCustomerEmail($customer->getEmail());
        }
        
        if ($selection->isSelected(PersonalDataSelectionItem::ORDERS)) {
            $this->deleteOrdersByOrderList($orderList);
        }
        
        if ($selection->isSelected(PersonalDataSelectionItem::BASE_DATA)) {
            $this->deleteAddressesByCustomer($customer);
            $this->deleteCustomerById($customerId);
        }
        
        if ($selection->isSelected(PersonalDataSelectionItem::AGREEMENTS)) {
            $this->deleteAgreementsByCustomer($customer);
        }
    }
    
    
    /**
     * Exports all personal data of a given customer as XML including the following data:
     *
     * - base data
     * - addresses
     * - orders
     * - invoices
     * - packing slips
     * - withdrawals
     * - agreements
     * - emails
     * - shopping carts
     * - shared shopping carts
     * - reviews
     * - newsletter subscriptions
     *
     * The XML file and all invoices and packing slips of the customer are compressed to a ZIP file.
     * The path to the ZIP file is then returned.
     *
     * @param \IdType               $customerId
     * @param PersonalDataSelection $selection Personal data selection
     *
     * @return string .
     * @throws InvalidArgumentException
     *
     */
    public function exportPersonalDataByCustomerId(IdType $customerId, PersonalDataSelection $selection)
    {
        $personalData                  = [];
        $personalData['invoices']      = new InvoiceListItemCollection([]);
        $personalData['packing_slips'] = new PackingSlipCollection([]);
        $customer                      = $this->getCustomerById($customerId);
        $orderList                     = $this->getOrderListByCustomerId($customerId);
        
        if ($selection->isSelected(PersonalDataSelectionItem::BASE_DATA)) {
            $personalData['base_data'] = $customer;
            $personalData['addresses'] = $this->getAddressesByCustomer($customer);
        }
        
        if ($selection->isSelected(PersonalDataSelectionItem::ORDERS)) {
            $personalData['orders']        = $this->getOrdersByOrderList($orderList);
            $personalData['invoices']      = $this->getInvoicesByCustomerId($customerId);
            $personalData['packing_slips'] = $this->getPackingSlipsByOrderList($orderList);
        }
        
        if ($selection->isSelected(PersonalDataSelectionItem::WITHDRAWALS)) {
            $personalData['withdrawals'] = $this->getWithdrawalsByOrderList($orderList);
        }
        
        if ($selection->isSelected(PersonalDataSelectionItem::AGREEMENTS)) {
            $personalData['agreements'] = $this->getAgreementsByCustomer($customer);
        }
        
        if ($selection->isSelected(PersonalDataSelectionItem::EMAILS)) {
            $personalData['emails'] = $this->getEmailsByCustomerEmailAddress($customer->getEmail());
        }
        
        if ($selection->isSelected(PersonalDataSelectionItem::CARTS)) {
            $personalData['shared_shopping_carts'] = $this->getSharedShoppingCartsByCustomerId($customerId);
            $personalData['shopping_cart']         = $this->getShoppingCartsByCustomerId($customerId);
        }
        
        if ($selection->isSelected(PersonalDataSelectionItem::REVIEWS)) {
            $personalData['reviews'] = $this->getReviewsByCustomerId($customerId);
        }
        
        if ($selection->isSelected(PersonalDataSelectionItem::NEWSLETTER_SUBSCRIPTIONS)) {
            $newsletterSubscription = $this->getNewsletterSubscriptionByCustomerEmail($customer->getEmail());
            if ($newsletterSubscription !== null) {
                $personalData['newsletter_subscription'] = $newsletterSubscription;
            }
        }
        
        $xmlString    = $this->xmlSerializer->serialize($personalData)->asXML();
        $customerHash = $customer->getId() . '-' . md5(time());
        $zipFilePath  = $this->serviceSettings->getZipDownloadFilePath() . $customerHash . '.zip';
        
        $this->buildZipFile(new StringType($customerHash),
                            new StringType($xmlString),
                            $this->invoiceFileStorage->getFileListByInvoiceList($personalData['invoices']),
                            $this->packingSlipFileStorage->getFileListByPackingSlipList($personalData['packing_slips']));
        
        return $zipFilePath;
    }
    
    
    /**
     * @param \InvoiceArchiveReadServiceInterface $invoiceArchiveReadService
     */
    public function setInvoiceArchiveReadService(InvoiceArchiveReadServiceInterface $invoiceArchiveReadService)
    {
        $this->invoiceArchiveReadService = $invoiceArchiveReadService;
    }
    
    
    /**
     * @param \InvoiceArchiveWriteServiceInterface $invoiceArchiveWriteService
     */
    public function setInvoiceArchiveWriteService(InvoiceArchiveWriteServiceInterface $invoiceArchiveWriteService)
    {
        $this->invoiceArchiveWriteService = $invoiceArchiveWriteService;
    }
    
    
    /**
     * @param \PackingSlipServiceInterface $packingSlipService
     */
    public function setPackingSlipService(PackingSlipServiceInterface $packingSlipService)
    {
        $this->packingSlipService = $packingSlipService;
    }
    
    
    /**
     * @param \StringType             $customerHash
     * @param \StringType             $xmlString
     * @param \ExistingFileCollection $invoices
     * @param \ExistingFileCollection $packingSlips
     *
     * @throws InvalidArgumentException
     */
    protected function buildZipFile(
        StringType $customerHash,
        StringType $xmlString,
        ExistingFileCollection $invoices,
        ExistingFileCollection $packingSlips
    ) {
        $directoryPath = new StringType($this->serviceSettings->getExportFilePath() . $customerHash->asString() . '/');
        $this->createCustomerDirectory($directoryPath);
        $this->createXmlFile($directoryPath, $xmlString);
        $this->copyFilesToExport($directoryPath, $invoices);
        $this->copyFilesToExport($directoryPath, $packingSlips);
        $this->createZipFile($directoryPath, $customerHash);
    }
    
    
    /**
     * @param \StringType $directoryPath
     *
     * @throws \DirectoryCreationFailedException
     */
    protected function createCustomerDirectory(StringType $directoryPath)
    {
        if (!@mkdir($directoryPath->asString()) && !is_dir($directoryPath->asString())) {
            throw new DirectoryCreationFailedException;
        }
    }
    
    
    /**
     * @param \StringType $directoryPath
     * @param \StringType $xmlString
     */
    protected function createXmlFile(StringType $directoryPath, StringType $xmlString)
    {
        file_put_contents($directoryPath->asString() . 'export-' . md5(time()) . '.xml',
                          $xmlString->asString());
    }
    
    
    /**
     * @param \StringType             $directoryPath
     * @param \ExistingFileCollection $files
     */
    protected function copyFilesToExport(StringType $directoryPath, ExistingFileCollection $files)
    {
        /**
         * @var ExistingFile $file
         */
        foreach ($files as $file) {
            copy($file->getFilePath(), $directoryPath->asString() . basename($file->getFilePath()));
        }
    }
    
    
    /**
     * @param \StringType $customerHash
     */
    protected function createZipFile(StringType $directoryPath, StringType $customerHash)
    {
        $fileNames   = glob($directoryPath->asString() . '*.*');
        $zipFilePath = $this->serviceSettings->getExportZipFilePath() . $customerHash->asString() . '.zip';
        $zipFile     = new PclZip($zipFilePath);
        
        foreach ($fileNames as $key => $fileName) {
            // remove filenames like ".."
            if (strpos(basename($fileName), '.') === 0) {
                unset($fileNames[$key]);
            }
        }
        
        $zipFile->add(implode(',', $fileNames), PCLZIP_OPT_REMOVE_ALL_PATH);
    }
    
    
    /**
     * @param \OrderListItemCollection $orderList
     *
     * @throws InvalidArgumentException
     */
    protected function deleteOrdersByOrderList(OrderListItemCollection $orderList)
    {
        /**
         * @var OrderListItem $orderListItem
         */
        foreach ($orderList as $orderListItem) {
            $this->orderWriteService->removeOrderById(new IdType($orderListItem->getOrderId()));
        }
    }
    
    
    /**
     * @param \IdType $customerId
     *
     * @throws InvalidArgumentException
     */
    protected function deleteInvoicesByCustomerId(IdType $customerId)
    {
        if ($this->invoiceArchiveReadService === null || $this->invoiceArchiveWriteService === null) {
            return;
        }
        
        $invoiceList = $this->invoiceArchiveReadService->getInvoiceListByConditions(['invoices.customer_id' => $customerId->asInt()]);
        
        /**
         * @var InvoiceListItem $invoiceListItem
         */
        foreach ($invoiceList as $invoiceListItem) {
            $this->invoiceArchiveWriteService->deleteInvoiceById(new IdType($invoiceListItem->getInvoiceId()));
        }
    }
    
    
    /**
     * @param \OrderListItemCollection $orderList
     *
     * @throws InvalidArgumentException
     */
    protected function deletePackingSlipsByOrderList(OrderListItemCollection $orderList)
    {
        if ($this->packingSlipService === null) {
            return;
        }
        
        /**
         * @var OrderListItem $orderListItem
         */
        foreach ($orderList as $orderListItem) {
            $this->packingSlipService->deletePackingSlipsByOrderId(new IdType($orderListItem->getOrderId()));
        }
    }
    
    
    /**
     * @param \CustomerEmail $customerEmail
     */
    protected function deleteEmailsByCustomerEmailAddress(CustomerEmail $customerEmail)
    {
        $this->emailService->deleteEmailsByCustomerEmail($customerEmail);
    }
    
    
    /**
     * @param \OrderListItemCollection $orderList
     */
    protected function deleteWithdrawalsByOrderList(OrderListItemCollection $orderList)
    {
        /**
         * @var OrderListItem $orderListItem
         */
        foreach ($orderList as $orderListItem) {
            $withdrawalIds = $orderListItem->getWithdrawalIds();
            
            /**
             * @var IdType $withdrawalId
             */
            foreach ($withdrawalIds as $withdrawalId) {
                $this->withdrawalWriteService->delete($this->withdrawalReadService->getById($withdrawalId));
                $this->eventDispatcher->dispatch($this->createWithdrawalDeletedEvent($withdrawalId));
            }
        }
    }
    
    
    /**
     * @param \IdType $customerId
     */
    protected function deleteReviewsByCustomerId(IdType $customerId)
    {
        $reviews = $this->reviewReadService->getReviewsByCustomerId($customerId);
        
        /**
         * @var Review $review
         */
        foreach ($reviews as $review) {
            $this->reviewWriteService->delete($review);
        }
    }
    
    
    /**
     * @param \IdType $customerId
     */
    protected function deleteShoppingCartsByCustomerId(IdType $customerId)
    {
        $this->shoppingCartService->deleteShoppingCartsByCustomerId($customerId);
    }
    
    
    /**
     * @param \IdType $customerId
     */
    protected function deleteSharedShoppingCartsByCustomerId(IdType $customerId)
    {
        $this->sharedShoppingCartService->deleteShoppingCartsByCustomerId($customerId);
    }
    
    
    /**
     * @param \CustomerEmailInterface $email
     */
    protected function deleteNewsletterSubscriptionByCustomerEmail(CustomerEmailInterface $email)
    {
        $this->newsletterSubscriptionService->unsubscribe($email);
    }
    
    
    /**
     * @param \CustomerInterface $customer
     */
    protected function deleteAddressesByCustomer(CustomerInterface $customer)
    {
        $addresses = $this->addressBookService->getCustomerAddresses($customer);
        
        /**
         * @var CustomerAddress $address
         */
        foreach ($addresses as $address) {
            $this->addressBookService->deleteAddress($address);
        }
    }
    
    
    /**
     * @param \IdType $customerId
     */
    protected function deleteCustomerById(IdType $customerId)
    {
        $this->customerService->deleteCustomerById($customerId);
        $this->eventDispatcher->dispatch($this->createCustomerDeletedEvent($customerId));
    }
    
    
    /**
     * @param \CustomerInterface $customer
     */
    protected function deleteAgreementsByCustomer(CustomerInterface $customer)
    {
        $agreements = $this->agreementReadService->getAgreementsByCustomerEmail(new StringType((string)$customer->getEmail()));
        
        foreach ($agreements as $agreement) {
            $this->agreementWriteService->delete($agreement);
        }
    }
    
    
    /**
     * @param \IdType $customerId
     *
     * @return \Customer
     */
    protected function getCustomerById(IdType $customerId)
    {
        return $this->customerReadService->getCustomerById($customerId);
    }
    
    
    /**
     * @param \IdType $customerId
     *
     * @return \OrderListItemCollection
     */
    protected function getOrderListByCustomerId(IdType $customerId)
    {
        return $this->orderReadService->getOrderListByCustomerId($customerId);
    }
    
    
    protected function getOrdersByOrderList(OrderListItemCollection $orderList)
    {
        $orders = [];
        
        /**
         * @var OrderListItem $order
         */
        foreach ($orderList as $order) {
            $orders[] = $this->orderReadService->getOrderById(new IdType($order->getOrderId()));
        }
        
        return $orders;
    }
    
    
    /**
     * @param \Customer $customer
     *
     * @return array
     */
    protected function getAddressesByCustomer(Customer $customer)
    {
        return $this->addressBookService->getCustomerAddresses($customer);
    }
    
    
    /**
     * @param \IdType $customerId
     *
     * @return \InvoiceListItemCollection
     */
    protected function getInvoicesByCustomerId(IdType $customerId)
    {
        if ($this->invoiceArchiveReadService === null || $this->invoiceArchiveWriteService === null) {
            return new InvoiceListItemCollection();
        }
        
        return $this->invoiceArchiveReadService->getInvoiceListByConditions(['customer_id' => $customerId->asInt()]);
    }
    
    
    /**
     * @param \OrderListItemCollection $orders
     *
     * @return \PackingSlipCollection
     */
    protected function getPackingSlipsByOrderList(OrderListItemCollection $orders)
    {
        if ($this->packingSlipService === null) {
            return new PackingSlipCollection();
        }
        
        return $this->packingSlipService->getPackingSlipsByOrderList($orders);
    }
    
    
    /**
     * @param \OrderListItemCollection $orderList
     *
     * @return \WithdrawalCollection
     */
    protected function getWithdrawalsByOrderList(OrderListItemCollection $orderList)
    {
        $withdrawals = [];
        
        /**
         * @var OrderListItem $orderListItem
         */
        foreach ($orderList as $orderListItem) {
            $withdrawalIds = $orderListItem->getWithdrawalIds();
            
            /**
             * @var IdType $withdrawalId
             */
            foreach ($withdrawalIds as $withdrawalId) {
                $withdrawals[] = $this->withdrawalReadService->getById($withdrawalId);
            }
        }
        
        return new WithdrawalCollection($withdrawals);
    }
    
    
    /**
     * @param \CustomerInterface $customer
     *
     * @return \AgreementCollection
     */
    protected function getAgreementsByCustomer(CustomerInterface $customer)
    {
        return $this->agreementReadService->getAgreementsByCustomerEmail(new StringType((string)$customer->getEmail()));
    }
    
    
    /**
     * @param \CustomerEmail $email
     *
     * @return \EmailCollection
     */
    protected function getEmailsByCustomerEmailAddress(CustomerEmail $email)
    {
        return $this->emailService->getEmailsByCustomerEmail($email);
    }
    
    
    /**
     * @param \IdType $customerId
     *
     * @return \SharedShoppingCartCollection
     */
    protected function getSharedShoppingCartsByCustomerId(IdType $customerId)
    {
        return $this->sharedShoppingCartService->getShoppingCartsByCustomerId($customerId);
    }
    
    
    /**
     * @param \IdType $customerId
     *
     * @return \ShoppingCartCollection
     */
    protected function getShoppingCartsByCustomerId(IdType $customerId)
    {
        return $this->shoppingCartService->getShoppingCartsByCustomerId($customerId);
    }
    
    
    /**
     * @param \IdType $customerId
     *
     * @return \ReviewCollection
     */
    protected function getReviewsByCustomerId(IdType $customerId)
    {
        return $this->reviewReadService->getReviewsByCustomerId($customerId);
    }
    
    
    /**
     * @param \CustomerEmail $email
     *
     * @return bool|\NewsletterSubscription
     */
    protected function getNewsletterSubscriptionByCustomerEmail(CustomerEmail $email)
    {
        return $this->newsletterSubscriptionService->getSubscriptionByCustomerEmail($email);
    }
    
    
    /**
     * @param IdType $customerId
     *
     * @return CustomerDeleted
     */
    protected function createCustomerDeletedEvent(IdType $customerId): CustomerDeleted
    {
        return CustomerDeleted::create($this->customerFactory->createCustomerId($customerId->asInt()));
    }
    
    
    /**
     * @param IdType $withdrawalId
     *
     * @return WithdrawalDeleted
     */
    protected function createWithdrawalDeletedEvent(IdType $withdrawalId): WithdrawalDeleted
    {
        return WithdrawalDeleted::create($this->withdrawalFactory->createWithdrawalId($withdrawalId->asInt()));
    }
}