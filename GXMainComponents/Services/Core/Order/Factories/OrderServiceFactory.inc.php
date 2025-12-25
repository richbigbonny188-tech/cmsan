<?php
/* --------------------------------------------------------------
   OrderServiceFactory.php 2015-12-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('AbstractOrderServiceFactory');

/**
 * Class OrderServiceFactory
 *
 * @category   System
 * @package    Order
 * @subpackage Factories
 */
class OrderServiceFactory extends AbstractOrderServiceFactory
{
    /**
     * Query builder.
     * @var CI_DB_query_builder
     */
    protected $db;
    
    /**
     * @var \PaymentTitleProvider
     */
    protected $paymentTitleProvider;
    
    /**
     * @var \ShippingTitleProvider
     */
    protected $shippingTitleProvider;
    
    /**
     * @var \OrderServiceSettings
     */
    protected $orderServiceSettings;
    
    /**
     * @var \DeleteHistoryWriteService
     */
    protected $deleteHistoryWriteService;
    
    /**
     * @var \DeleteHistoryRepositoryInterface
     */
    protected $deleteHistoryRepository;
    
    
    /**
     * OrderServiceFactory constructor.
     *
     * @param CI_DB_query_builder $db Query builder.
     */
    public function __construct(CI_DB_query_builder $db)
    {
        $this->db = $db;
    }
    
    
    /**
     * Creates and returns an order write service object.
     *
     * @return OrderWriteService New order write service object.
     */
    public function createOrderWriteService()
    {
        $orderRepository                     = $this->_createOrderRepository();
        $orderItemRepository                 = $this->_createOrderItemRepository();
        $orderItemAttributeRepositoryFactory = $this->_createOrderItemAttributeRepositoryFactory();
        $orderTotalRepository                = $this->_createOrderTotalRepository();
        $orderStatusHistoryReader            = $this->_createOrderStatusHistoryStorage();
        $orderServiceSettings                = $this->_createOrderServiceSettings();
        $deleteHistoryWriteService           = $this->_createDeleteHistoryWriteService();
        
        return MainFactory::create(OrderWriteService::class,
                                   $orderRepository,
                                   $orderItemRepository,
                                   $orderItemAttributeRepositoryFactory,
                                   $orderTotalRepository,
                                   $orderStatusHistoryReader,
                                   $orderServiceSettings,
                                   $deleteHistoryWriteService);
    }
    
    
    /**
     * Creates and returns an order read service object.
     *
     * @return OrderReadService New order read service object.
     */
    public function createOrderReadService()
    {
        $orderRepository     = $this->_createOrderRepository();
        $orderItemRepository = $this->_createOrderItemRepository();
        $orderListGenerator  = $this->_createOrderListGenerator();
        
        return MainFactory::create(OrderReadService::class,
                                   $orderRepository,
                                   $orderItemRepository,
                                   $orderListGenerator);
    }
    
    
    /**
     * Creates and returns an order object service.
     *
     * @return OrderObjectService New order object service.
     */
    public function createOrderObjectService()
    {
        $orderItemFactory          = MainFactory::create(OrderItemFactory::class);
        $orderItemAttributeFactory = MainFactory::create(OrderItemAttributeFactory::class);
        $orderItemPropertyFactory  = MainFactory::create(OrderItemPropertyFactory::class);
        $orderTotalFactory         = MainFactory::create(OrderTotalFactory::class);
        
        return MainFactory::create(OrderObjectService::class,
                                   $orderItemFactory,
                                   $orderItemAttributeFactory,
                                   $orderItemPropertyFactory,
                                   $orderTotalFactory);
    }
    
    
    /**
     * Creates and returns an order repository.
     *
     * @return OrderRepository New order repository.
     */
    protected function _createOrderRepository()
    {
        $orderFactory             = MainFactory::create(OrderFactory::class);
        $countryService           = StaticGXCoreLoader::getService('Country');
        $orderRepositoryReader    = MainFactory::create(OrderRepositoryReader::class,
                                                        $this->db,
                                                        $orderFactory,
                                                        $countryService,
                                                        $this->_createPaymentTitleProvider());
        $orderRepositoryWriter    = MainFactory::create(OrderRepositoryWriter::class, $this->db);
        $orderRepositoryDeleter   = MainFactory::create(OrderRepositoryDeleter::class, $this->db);
        $orderItemRepository      = $this->_createOrderItemRepository();
        $orderTotalRepository     = $this->_createOrderTotalRepository();
        $orderStatusHistoryReader = $this->_createOrderStatusHistoryStorage();
        $addonValueService        = MainFactory::create(AddonValueService::class,
                                                        MainFactory::create(AddonValueStorageFactory::class,
                                                                            $this->db));
        
        return MainFactory::create(OrderRepository::class,
                                   $orderFactory,
                                   $orderRepositoryWriter,
                                   $orderRepositoryReader,
                                   $orderRepositoryDeleter,
                                   $orderItemRepository,
                                   $orderTotalRepository,
                                   $orderStatusHistoryReader,
                                   $addonValueService);
    }
    
    
    /**
     * Creates and returns an order item repository.
     *
     * @return OrderItemRepository New order item repository.
     */
    protected function _createOrderItemRepository()
    {
        $orderItemFactory                    = MainFactory::create(OrderItemFactory::class);
        $orderItemRepositoryReader           = MainFactory::create(OrderItemRepositoryReader::class,
                                                                   $this->db,
                                                                   $orderItemFactory);
        $orderItemRepositoryWriter           = MainFactory::create(OrderItemRepositoryWriter::class, $this->db);
        $orderItemRepositoryDeleter          = MainFactory::create(OrderItemRepositoryDeleter::class, $this->db);
        $orderItemAttributeRepositoryFactory = $this->_createOrderItemAttributeRepositoryFactory();
        $addonValueService                   = MainFactory::create(AddonValueService::class,
                                                                   MainFactory::create(AddonValueStorageFactory::class,
                                                                                       $this->db));
        
        return MainFactory::create('OrderItemRepository',
                                   $orderItemAttributeRepositoryFactory,
                                   $orderItemRepositoryReader,
                                   $orderItemRepositoryWriter,
                                   $orderItemRepositoryDeleter,
                                   $addonValueService);
    }
    
    
    /**
     * Creates and returns and order item attribute repository factory.
     *
     * @return OrderItemAttributeRepositoryFactory New order item attribute repository factory.
     */
    protected function _createOrderItemAttributeRepositoryFactory()
    {
        return MainFactory::create(OrderItemAttributeRepositoryFactory::class, $this->db);
    }
    
    
    /**
     * Creates and returns an order total repository.
     *
     * @return OrderTotalRepository New order total repository.
     */
    protected function _createOrderTotalRepository()
    {
        $orderTotalFactory           = MainFactory::create(OrderTotalFactory::class);
        $orderTotalRepositoryReader  = MainFactory::create(OrderTotalRepositoryReader::class,
                                                           $this->db,
                                                           $orderTotalFactory);
        $orderTotalRepositoryWriter  = MainFactory::create(OrderTotalRepositoryWriter::class, $this->db);
        $orderTotalRepositoryDeleter = MainFactory::create(OrderTotalRepositoryDeleter::class, $this->db);
        
        return MainFactory::create(OrderTotalRepository::class,
                                   $orderTotalRepositoryReader,
                                   $orderTotalRepositoryWriter,
                                   $orderTotalRepositoryDeleter);
    }
    
    
    /**
     * Creates and returns a order status history storage.
     *
     * @return OrderStatusHistoryStorage New order status history storage.
     */
    protected function _createOrderStatusHistoryStorage()
    {
        return MainFactory::create(OrderStatusHistoryStorage::class, $this->db);
    }
    
    
    /**
     * Creates and returns an order list generator.
     *
     * @return OrderListGenerator New order list generator.
     */
    protected function _createOrderListGenerator()
    {
        return MainFactory::create(OrderListGenerator::class,
                                   $this->db,
                                   $this->_createPaymentTitleProvider(),
                                   $this->_createShippingTitleProvider());
    }
    
    
    /**
     * Creates a order service settings object
     *
     * @return OrderServiceSettings New order service settings object
     */
    protected function _createOrderServiceSettings()
    {
        if (null === $this->orderServiceSettings) {
            $configProvider = new OrderConfigurationProvider($this->db);
            
            $this->orderServiceSettings = new OrderServiceSettings($this->_int($configProvider->defaultOrderStatusId()),
                                                                   $this->_int($configProvider->defaultCustomerStatusId()),
                                                                   $this->_int($configProvider->defaultGuestStatusId()));
        }
        
        return $this->orderServiceSettings;
    }
    
    
    /**
     * Creates, in memory caches and returns the payment title provider.
     *
     * @return \PaymentTitleProvider
     */
    protected function _createPaymentTitleProvider()
    {
        if (null === $this->paymentTitleProvider) {
            $this->paymentTitleProvider = MainFactory::create(PaymentTitleProvider::class);
        }
        
        return $this->paymentTitleProvider;
    }
    
    
    /**
     * Creates, in memory caches and returns the shipping title provider.
     *
     * @return \ShippingTitleProvider
     */
    protected function _createShippingTitleProvider()
    {
        if (null === $this->shippingTitleProvider) {
            $this->shippingTitleProvider = MainFactory::create(ShippingTitleProvider::class);
        }
        
        return $this->shippingTitleProvider;
    }
    
    
    /**
     * Returns a new integer type.
     *
     * @param string $int Representing value,
     *
     * @return \IntType
     */
    protected function _int($int)
    {
        return new \IntType($int);
    }
    
    
    /**
     * Returns a new delete history write service.
     *
     * @return \DeleteHistoryWriteService
     */
    protected function _createDeleteHistoryWriteService()
    {
        
        if (null === $this->deleteHistoryWriteService) {
            $this->deleteHistoryWriteService = DeleteHistoryServiceFactory::writeService();
        }
        
        return $this->deleteHistoryWriteService;
    }
}