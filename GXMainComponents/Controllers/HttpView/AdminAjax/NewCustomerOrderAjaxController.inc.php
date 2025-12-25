<?php
/*--------------------------------------------------------------
   NewCustomerOrderAjaxController.inc.php 2023-03-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

use Gambio\Admin\Modules\AccessGroup\Model\ValueObjects\AccessGroupItem;
use Gambio\Admin\Modules\AccessRole\Model\ValueObjects\PermissionAction;
use Gambio\Core\Permission\Services\PermissionService;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Class NewCustomerOrderAjaxController
 */
class NewCustomerOrderAjaxController extends AdminHttpViewController
{
    protected PermissionService $adminAccessService;
    protected LanguageTextManager $textManager;
    
    
    /**
     * @param HttpContextReaderInterface     $httpContextReader
     * @param HttpResponseProcessorInterface $httpResponseProcessor
     * @param ContentViewInterface           $defaultContentView
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __construct(
        HttpContextReaderInterface $httpContextReader,
        HttpResponseProcessorInterface $httpResponseProcessor,
        ContentViewInterface $defaultContentView
    ) {
        parent::__construct($httpContextReader, $httpResponseProcessor, $defaultContentView);
    
        $this->adminAccessService = LegacyDependencyContainer::getInstance()->get(PermissionService::class);
        $this->textManager        = MainFactory::create(LanguageTextManager::class);
    }
    
    
    /**
     * @return JsonHttpControllerResponse
     */
    public function actionDefault(): JsonHttpControllerResponse
    {
        ['customerId' => $customerId] = $this->_getPostDataCollection()->getArray();
    
        try {
            $success = $this->userHasPermissionsToCreateAnOrder();
            $data    = $success ? $this->createOrderForCustomer((int)$customerId) : ['message' => 'insufficient permissions'];
        } catch (Exception $exception) {
            $success = false;
            $data    = ['message' => $exception->getMessage()];
        }
    
        return MainFactory::create('JsonHttpControllerResponse', ['success' => $success, 'data' => $data]);
    }
    
    
    /**
     * @param int $customerId
     *
     * @return array
     */
    protected function createOrderForCustomer(int $customerId): array
    {
        $customers1_query = xtc_db_query("select * from ".TABLE_CUSTOMERS." where customers_id = '".xtc_db_input($customerId)."'");
        $customers1 = xtc_db_fetch_array($customers1_query);
    
        $customers_query = xtc_db_query("select * from ".TABLE_ADDRESS_BOOK." where customers_id = '".xtc_db_input($customerId)."'");
        $customers = xtc_db_fetch_array($customers_query);
    
        $stat_query = xtc_db_query("select * from ".TABLE_CUSTOMERS_STATUS." where customers_status_id = '".$customers1['customers_status']."' and language_id = " . (int)$_SESSION['languages_id']);
        $stat = xtc_db_fetch_array($stat_query);
    
        $coo_country_service   = StaticGXCoreLoader::getService('Country');
        $is_state_mandatory    = $coo_country_service->isStateMandatory(new IdType($customers['entry_country_id']));
        $country               = $coo_country_service->getCountryById(new IdType($customers['entry_country_id']));
        $entry_state_has_zones = $coo_country_service->countryHasCountryZones($country);
    
        if ((!$is_state_mandatory && ACCOUNT_STATE === 'false')
            || (ACCOUNT_STATE === 'true'
                && !$entry_state_has_zones)) {
            $customers['entry_suburb'] = '';
        }
        
        $this->textManager->init_from_lang_file('lang/' . $_SESSION['language'] . '/modules/order_total/ot_total_netto.php');
    
        require_once(DIR_FS_CATALOG . 'includes/modules/order_total/ot_total_netto.php');
        
        $textSubTotal       = $this->textManager->get_text('TEXT_SUBTOTAL', 'admin_customers');
        $coo_ot_total_netto = new ot_total_netto();
        $sql_data_array     = [
            'orders_id'  => null,
            'title'      => '<b>' . $textSubTotal . '</b>:',
            'text'       => '0',
            'value'      => '0',
            'class'      => 'ot_subtotal',
            'sort_order' => MODULE_ORDER_TOTAL_SUBTOTAL_SORT_ORDER,
        ];
    
        /**
         * BEGIN NEW ORDER SERVICE
         */
    
        /** @var OrderWriteService $orderWriteService */
        $orderWriteService = StaticGXCoreLoader::getService('OrderWrite');
    
        $isGuest = new BoolType($customers1['customers_status'] == DEFAULT_CUSTOMERS_STATUS_ID_GUEST);
    
        $customerStatusInfo = MainFactory::create('CustomerStatusInformation',
                                                  new IdType((int)$customers1['customers_status']),
                                                  new StringType((string)$stat['customers_status_name']),
                                                  new StringType((string)$stat['customers_status_image']),
                                                  new DecimalType((double)$stat['customers_status_discount']), $isGuest);
    
        /** @var CustomerReadService $customerReadService */
        $customerReadService = StaticGXCoreLoader::getService('CustomerRead');
    
        /** @var Customer $customer */
        $customer = $customerReadService->getCustomerById(new IdType($customers['customers_id']));
    
        $customerAddress = $customer->getDefaultAddress();
    
        /** @var AddressBookService $addressBookService */
        $addressBookService = StaticGXCoreLoader::getService('AddressBook');
    
        $addressBookId  = new IdType($customers['address_book_id']);
        $address = $addressBookService->findAddressById($addressBookId);
    
        $orderTotalObjects = array();
    
        /** @var OrderObjectService $orderObjectService */
        $orderObjectService = StaticGXCoreLoader::getService('OrderObject');
    
        $orderTotalObjects[] = $orderObjectService->createOrderTotalObject(new StringType('<b>' . $textSubTotal . ':</b>'),
                                                                           new DecimalType(0),
                                                                           new StringType('0'),
                                                                           new StringType('ot_subtotal'),
                                                                           MainFactory::create('IntType',
                                                                                               (int)MODULE_ORDER_TOTAL_SUBTOTAL_SORT_ORDER));
    
        if($coo_ot_total_netto->check())
        {
            $orderTotalObjects[] = $orderObjectService->createOrderTotalObject(new StringType($coo_ot_total_netto->title . ':'),
                                                                               new DecimalType(0),
                                                                               new StringType('0'),
                                                                               new StringType($coo_ot_total_netto->code),
                                                                               MainFactory::create('IntType',
                                                                                                   (int)$coo_ot_total_netto->sort_order));
        }
        
        $textTotal = $this->textManager->get_text('TEXT_TOTAL', 'admin_customers');
    
        $orderTotalObjects[] = $orderObjectService->createOrderTotalObject(new StringType('<b>' . $textTotal . ':</b>'),
                                                                           new DecimalType(0),
                                                                           new StringType('0'),
                                                                           new StringType('ot_total'),
                                                                           MainFactory::create('IntType',
                                                                                               (int)MODULE_ORDER_TOTAL_TOTAL_SORT_ORDER));
    
        $orderTotals = MainFactory::create('OrderTotalCollection', $orderTotalObjects);
    
        $orderId = $orderWriteService->createNewCustomerOrder(new IdType($customers['customers_id']),
                                                              $customerStatusInfo,
                                                              new StringType((string)$customers1['customers_cid']),
                                                              new EmailStringType((string)$customers1['customers_email_address']),
                                                              new StringType((string)$customers1['customers_telephone']),
                                                              new StringType((string)$customers1['customers_vat_id']),
                                                              $customerAddress, $address, $address,
                                                              MainFactory::create('OrderItemCollection', array()),
                                                              $orderTotals, MainFactory::create('OrderShippingType',
                                                                                                new StringType('Pauschale Versandkosten'),
                                                                                                new StringType('flat_flat')),
                                                              MainFactory::create('OrderPaymentType',
                                                                                  new StringType(gm_get_conf('MANUAL_ORDER_PAYMENT') ?: 'cod'),
                                                                                  new StringType(gm_get_conf('MANUAL_ORDER_PAYMENT') ?: 'cod')),
                                                              MainFactory::create('CurrencyCode',
                                                                                  new NonEmptyStringType(DEFAULT_CURRENCY)),
                                                              new LanguageCode(new NonEmptyStringType($_SESSION['language_code'])),
                                                              new DecimalType(0.0), new StringType(''));
        /**
         * createNewCustomerOrder automatically creates an entry in the order status history
         * that the customer has been notified. In this case the customer has not been notified
         *
         * So the following code deletes the complete history and will add the read out content
         * to the database with the difference that the customer_notified is set to false
         */
        $orderReadService = StaticGXCoreLoader::getService('OrderRead');
        $orderIdIdType = new IdType($orderId);
        /** @var GXEngineOrder $createdOrder */
        $createdOrder = $orderReadService->getOrderById($orderIdIdType);
        $statusId = new IntType($createdOrder->getStatusId());
        $comment = new StringType($createdOrder->getComment());
        $customerNotified = new BoolType(false);
    
        $orderStatusHistoryStorage = MainFactory::create(OrderStatusHistoryStorage::class, StaticGXCoreLoader::getDatabaseQueryBuilder());
        $orderStatusHistoryStorage->deleteHistory($orderIdIdType);
        $orderStatusHistoryStorage->addStatusUpdate($orderIdIdType, $statusId, $comment, $customerNotified, new IdType($customerId));
        
        return ['orderId' => $orderIdIdType->asInt()];
    }
    
    
    /**
     * @return bool
     */
    protected function userHasPermissionsToCreateAnOrder(): bool
    {
        return $this->adminAccessService->checkAdminPermission((int)$_SESSION['customer_id'],
                                                  PermissionAction::READ,
                                                  AccessGroupItem::CONTROLLER_TYPE,
                                                  'OrdersOverview');
    }
}