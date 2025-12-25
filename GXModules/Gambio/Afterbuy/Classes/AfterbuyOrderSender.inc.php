<?php
/* --------------------------------------------------------------
   AfterbuyOrderSender.inc.php 2023-10-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

use GXModules\Gambio\Afterbuy\AfterbuyCommon\App\CountryCodesMapper;
use GXModules\Gambio\Afterbuy\AfterbuyCommon\Model\OrderId;
use GXModules\Gambio\Afterbuy\Classes\Events\AfterbuyOrderDataPrepared;
use GXModules\Gambio\Afterbuy\OrderExport\Service\AfterbuyOrderXmlApiService;
use GXModules\Gambio\Afterbuy\OrderStatus\Exceptions\AfterbuyOrderStatusPaidException;
use GXModules\Gambio\Afterbuy\OrderStatus\Service\AfterbuyCheckPaidStatusService;
use GXModules\Gambio\Afterbuy\ShopApi\Exceptions\AfterbuyOrderIdAlreadyMappedException;
use GXModules\Gambio\Afterbuy\ShopApi\Exceptions\AfterbuyOrderIdMappingException;
use GXModules\Gambio\Afterbuy\ShopApi\Service\AfterbuyOrderIdMappingService;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

require_once __DIR__ . '/AfterbuyException.inc.php';


/**
 * Class AfterbuyOrderSender
 *
 * @package GXModules\Gambio\Afterbuy\Classes
 */
class AfterbuyOrderSender
{
    protected const AFTERBUY_URL = 'https://api.afterbuy.de/afterbuy/ShopInterfaceUTF8.aspx';
    
    // Timeout for requests to AFTERBUY_URL
    protected const REQUEST_TIMEOUT = 10;
    
    // 0 = Feedbackdatum setzen und KEINE automatische Erstkontaktmail versenden
    // 1 = KEIN Feedbackdatum setzen, aber automatische Erstkontaktmail versenden
    //     (Achtung: Kunde müsste Feedback durchlaufen wenn die Erstkontakt nicht angepasst wird!)
    // 2 = Feedbackdatum setzen und automatische Erstkontaktmail versenden
    //     (Achtung: Erstkontaktmail muss mit Variablen angepasst werden!)
    protected const FEEDBACKDATUM = '0';
    
    // 1 = Versand aus Shop
    // 0 = Versandermittlung durch Afterbuy (nur wenn Stammartikel erkannt wird!)
    protected const VERSANDERMITTLUNG_AB = 1;
    
    // 0 = Standard EbayName (= gesamte Zeile "Benutzername" in dieser Datei)
    // 1 = E-Mail
    // 2 = EKNummer (wenn im Shop vorhanden!)
    protected const KUNDENERKENNUNG = '1';
    
    // ArtNr for generic order_total modules
    protected const GENERIC_OT_ARTNR = '99999999';
    
    
    /**
     * @var int
     */
    protected int $orderId;
    
    
    /**
     * @var string
     */
    protected string $partnerID;
    
    
    /**
     * @var string
     */
    protected $partnerPass;
    
    
    /**
     * @var string
     */
    protected $userID;
    
    
    /**
     * @var string
     */
    protected $orderStatus;
    
    
    /**
     * @var int[]
     */
    protected $b2bStatusIds;
    
    
    /**
     * @var array
     */
    protected $orderTotalConfiguration;
    
    
    /**
     * @var array
     */
    protected $paymentMapping;
    
    
    /**
     * @var AfterbuyLogger|mixed
     */
    protected $logger;
    
    
    /**
     * @var GambioAfterbuyConfigurationStorage|mixed
     */
    protected $configuration;
    
    
    /**
     * @var OrderInterface
     */
    protected $order;
    
    /**
     * @var string[]
     */
    protected array $orderTotalIgnore;
    
    
    /**
     * @var string
     */
    protected string $partnerToken;
    
    
    /**
     * @var string
     */
    protected string $accountToken;
    
    
    /**
     * @var EventDispatcherInterface|mixed
     */
    protected EventDispatcherInterface $eventDispatcher;
    
    
    /**
     * AfterbuyOrderSender constructor.
     *
     * Configuration will fall back on values from the old configuration page if GXModuleConfigurationStorage does not
     * contain values from the new configuration.
     *
     * @param $orderId
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __construct($orderId)
    {
        $this->configuration = MainFactory::create('GambioAfterbuyConfigurationStorage');
        
        $this->orderId      = (int)$orderId;
        $this->partnerID    = $this->configuration->get('partner_id');
        $this->partnerPass  = $this->configuration->get('partner_password');
        $this->userID       = $this->configuration->get('user_id');
        $this->partnerToken = $this->configuration->get('partner_token');
        $this->accountToken = $this->configuration->get('account_token');
        $this->orderStatus  = $this->configuration->get('order_status');
        $this->b2bStatusIds = [3];
        
        $this->logger = MainFactory::create('AfterbuyLogger');
        
        /** @var OrderReadService $orderService */
        $orderReadService = StaticGXCoreLoader::getService('OrderRead');
        /** @var OrderInterface $order */
        $this->order = $orderReadService->getOrderById(new IdType((int)$this->orderId));
        
        $this->orderTotalConfiguration = [];
        $this->addOrderTotalConfiguration('ot_bonus_fee', '99999991', 'Bonuspunkte', -1);
        $this->addOrderTotalConfiguration('ot_gambioultra', '99999992', 'Sperrgut');
        $this->addOrderTotalConfiguration('ot_loworderfee', '99999993', 'Mindermengenzuschlag');
        $this->addOrderTotalConfiguration('ot_ps_fee', '99999994', 'Mindermengenzuschlag');
        $this->addOrderTotalConfiguration('ot_payment', '99999995', 'Zahlartenrabatt');
        $this->addOrderTotalConfiguration('ot_coupon', '99999996', 'Kupon');
        $this->addOrderTotalConfiguration('ot_gv', '99999997', 'Gutschein', -1);
        $this->addOrderTotalConfiguration('ot_discount', '99999998', 'Rabatt');
        $this->addOrderTotalConfiguration('ot_cod_fee', '99999999', 'Nachnahme');
        
        $this->orderTotalIgnore = [
            'ot_gm_tax_free',
            'ot_subtotal',
            'ot_subtotal_no_tax',
            'ot_tax',
            'ot_total',
            'ot_total_netto',
        ];
        
        $this->paymentMapping = [];
        $this->addPaymentMapping('banktransfer', 'Bankeinzug', '7');
        $this->addPaymentMapping('cash', 'Barzahlung', '2');
        $this->addPaymentMapping('cod', 'Nachnahme', '4');
        $this->addPaymentMapping('invoice', 'Rechnung', '6');
        $this->addPaymentMapping('moneyorder', 'Überweisung/Vorkasse', '1');
        $this->addPaymentMapping('eustandardtransfer', 'Überweisung/Vorkasse', '1');
        $this->addPaymentMapping('moneybookers', 'Moneybookers', '15');
        $this->addPaymentMapping('moneybookers_cc', 'Moneybookers CC', '15');
        $this->addPaymentMapping('moneybookers_cgb', 'Moneybookers CGB', '15');
        $this->addPaymentMapping('moneybookers_csi', 'Moneybookers CSI', '15');
        $this->addPaymentMapping('moneybookers_elv', 'Moneybookers ELV', '15');
        $this->addPaymentMapping('moneybookers_giropay', 'Moneybookers GIROPAY', '15');
        $this->addPaymentMapping('moneybookers_ideal', 'Moneybookers IDEAL', '15');
        $this->addPaymentMapping('moneybookers_mae', 'Moneybookers MAE', '15');
        $this->addPaymentMapping('moneybookers_netpay', 'Moneybookers NETPAY', '15');
        $this->addPaymentMapping('moneybookers_psp', 'Moneybookers PSP', '15');
        $this->addPaymentMapping('moneybookers_pwy', 'Moneybookers PWY', '15');
        $this->addPaymentMapping('moneybookers_sft', 'Moneybookers SFT', '15');
        $this->addPaymentMapping('moneybookers_wlt', 'Moneybookers WLT', '15');
        $this->addPaymentMapping('paypal', 'PayPal', '5');
        $this->addPaymentMapping('paypalexpress', 'PayPal', '5');
        $this->addPaymentMapping('paypal_gambio', 'PayPal', '5');
        $this->addPaymentMapping('paypalgambio_alt', 'PayPal', '5');
        $this->addPaymentMapping('paypalng', 'PayPal', '5');
        $this->addPaymentMapping('paypal3', 'PayPal', '5');
        $this->addPaymentMapping('sofortueberweisung', 'Sofortüberweisung', '12');
        $this->addPaymentMapping('sofortueberweisungredirect', 'Sofortüberweisung', '12');
        $this->addPaymentMapping('sofortueberweisung_direct', 'Sofortüberweisung', '12');
        $this->addPaymentMapping('sofortueberweisungvorkasse', 'Sofortüberweisung', '12');
        $this->addPaymentMapping('sofort_sofortueberweisung', 'Sofortüberweisung', '12');
        $this->addPaymentMapping('billsafe', 'BillSAFE', '18');
        $this->addPaymentMapping('ipayment_cc', 'iPayment CC', '99');
        $this->addPaymentMapping('ipayment_elv', 'iPayment ELV', '99');
        $this->addPaymentMapping('cc', 'Kreditkarte', '99');
        $this->addPaymentMapping('amazonadvpay', 'AmazonPay', '99');
        $this->addPaymentMapping('default', 'sonstige Zahlungsweise', '99');
        $this->addPaymentMapping('CashHub', 'Barzahlung', '2');
        $this->addPaymentMapping('CashOnDeliveryHub', 'Nachnahme', '4');
        $this->addPaymentMapping('EasyCreditHub', 'EasyCredit', '');
        $this->addPaymentMapping('InvoiceHub', 'Rechnung', '6');
        $this->addPaymentMapping('KlarnaBanktransferHub', 'Klarna', '');
        $this->addPaymentMapping('KlarnaHub', 'Klarna', '');
        $this->addPaymentMapping('KlarnaPaylaterHub', 'Klarna', '');
        $this->addPaymentMapping('KlarnaPaynowHub', 'Klarna', '');
        $this->addPaymentMapping('KlarnaSliceitHub', 'Klarna', '');
        $this->addPaymentMapping('MoneyOrderHub', 'Überweisung/Vorkasse', '1');
        $this->addPaymentMapping('PayPal2Hub', 'PayPal', '5');
        $this->addPaymentMapping('PayPal2InstallmentsHub', 'PayPal', '5');
        $this->addPaymentMapping('PayPalHub', 'PayPal', '5');
        $this->addPaymentMapping('SofortHub', 'Sofortüberweisung', '12');
        
        $this->eventDispatcher = LegacyDependencyContainer::getInstance()->get(EventDispatcherInterface::class);
    }
    
    
    /**
     * @param $moduleCode
     * @param $artikelNr
     * @param $artikelName
     * @param $factor
     * @param $taxRate
     *
     * @return void
     */
    public function addOrderTotalConfiguration($moduleCode, $artikelNr, $artikelName, $factor = 1, $taxRate = 0): void
    {
        $this->orderTotalConfiguration[$moduleCode] = [
            'Artikelnr'   => $artikelNr,
            'Artikelname' => $artikelName,
            'ArtikelMwst' => $taxRate,
            'factor'      => $factor,
        ];
    }
    
    
    /**
     * @param $moduleCode
     * @param $afterbuyName
     * @param $afterbuyId
     *
     * @return void
     */
    public function addPaymentMapping($moduleCode, $afterbuyName, $afterbuyId): void
    {
        $this->paymentMapping[$moduleCode] = [
            'name' => $afterbuyName,
            'id'   => $afterbuyId,
        ];
    }
    
    
    /**
     * @return void
     *
     * @throws AfterbuyException
     * @throws Exception
     */
    public function processOrder(): void
    {
        $this->logger->notice('preparing data for order ' . $this->orderId);
        $data = $this->prepareData();
        
        $this->logger->notice('sending order ' . $this->orderId);
        try {
            $kundenNr = $this->sendData($data);
            if ($kundenNr !== null) {
                $this->logger->notice('transmission successful, order_id ' . $this->orderId . ' KundenNr ' . $kundenNr);
                $this->markOrderAsTransferred($this->orderId, $kundenNr);
                if ((int)$this->orderStatus !== -1) {
                    $comment = "Afterbuy KundenNr.: $kundenNr";
                    $this->updateOrderStatus($this->orderId, (int)$this->orderStatus, $comment);
                }
            }
            $this->callAfterbuyXmlApi();
        } catch (AfterbuyException $e) {
            $this->addOrderStatusHistoryEntry($this->orderId, $e->getMessage());
            throw $e;
        }
    }
    
    
    /**
     * @return void
     */
    protected function callAfterbuyXmlApi(): void
    {
        $container = LegacyDependencyContainer::getInstance();
        try {
            $xmlApiService = $container->get(AfterbuyOrderXmlApiService::class);
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            $expectedClass = AfterbuyOrderXmlApiService::class;
            $message       = "$expectedClass must be registered in the LegacyDependencyContainer!\nError: {$e->getMessage()}";
            $context       = array_merge($this->getContextForClassMember(),
                                         ['exception' => $this->getContextForThrowable($e)]);
            
            $this->logger->error($message, $context);
            
            return;
        }
        
        $xmlApiService->updateOrderViaXmlApi(new OrderId($this->orderId));
    }
    
    
    /**
     * Creates a context array from any throwable.
     * Serializes the throwable to an array.
     *
     * @param Throwable $throwable
     *
     * @return array
     */
    private function getContextForThrowable(Throwable $throwable): array
    {
        return [
            'message' => $throwable->getMessage(),
            'code'    => $throwable->getCode(),
            'file'    => $throwable->getFile(),
            'line'    => $throwable->getLine(),
            'trace'   => $throwable->getTrace(),
        ];
    }
    
    
    /**
     * Creates an array containing the class members.
     * Useful for the $context while logging.
     *
     * @return array
     */
    private function getContextForClassMember(): array
    {
        return [
            'orderId'      => $this->orderId,
            'partnerID'    => $this->partnerID,
            'partnerPass'  => $this->partnerPass,
            'userID'       => $this->userID,
            'partnerToken' => $this->partnerToken,
            'accountToken' => $this->accountToken,
            'orderStatus'  => $this->orderStatus,
            'b2bStatusIds' => $this->b2bStatusIds,
        ];
    }
    
    
    /**
     * @return array
     * @throws Exception
     */
    public function prepareData(): array
    {
        $customerAddress = $this->order->getCustomerAddress();
        $billingAddress  = $this->order->getBillingAddress();
        $deliveryAddress = $this->order->getDeliveryAddress();
        
        $salutations    = ['m' => 'Herr', 'f' => 'Frau'];
        $customerGender = (string)$billingAddress->getGender();
        
        $isB2B = in_array($this->order->getCustomerStatusInformation()->getStatusId(), $this->b2bStatusIds, true)
                 || $customerAddress->getB2BStatus()->getStatus();
        
        $customerStatusId = $this->order->getCustomerStatusInformation()->getStatusId();
        $db               = StaticGXCoreLoader::getDatabaseQueryBuilder();
        /** @var \CI_DB_mysqli_result $customersStatusRow */
        $customersStatusRow = $db->where('customers_status_id', $customerStatusId)
            ->get('customers_status', 1)
            ->row_array();
        $mustAddTax         = (bool)$customersStatusRow['customers_status_show_price_tax'] === false
                              && (bool)$customersStatusRow['customers_status_add_tax_ot'] === true;
        $separateTax        = (bool)$this->configuration->get('use_separate_tax');
        
        $street      = (string)$billingAddress->getStreet();
        $houseNumber = (string)$billingAddress->getHouseNumber();
        if (!empty($houseNumber)) {
            $street .= ' ' . $houseNumber;
        }
        
        $productSyncType = $this->configuration->get('product_sync_type');
        switch ($productSyncType) {
            case 'model_prodid':
                $artikelErkennung = '0';
                break;
            case 'pid_anr':
            case 'model_anr':
                $artikelErkennung = '1';
                break;
            case 'pid_ean':
            case 'model_ean':
            default:
                $artikelErkennung = '2';
                break;
        }
        
        $kLand = CountryCodesMapper::getLicensePlateCodeForIso2Code((string)$billingAddress->getCountry()->getIso2()) ??
                 'D';
        
        $data = [
            'Kundenerkennung'  => self::KUNDENERKENNUNG,
            'Action'           => 'new',
            'Kbenutzername'    => $this->order->getCustomerId() . '_XTC_' . $this->order->getOrderId(),
            'Kanrede'          => array_key_exists($customerGender, $salutations) ? $salutations[$customerGender] : '',
            'KFirma'           => (string)$billingAddress->getCompany(),
            'KVorname'         => (string)$billingAddress->getFirstname(),
            'KNachname'        => (string)$billingAddress->getLastname(),
            'KStrasse'         => $street,
            'KStrasse2'        => (string)$billingAddress->getAdditionalAddressInfo(),
            'KPLZ'             => (string)$billingAddress->getPostcode(),
            'KOrt'             => (string)$billingAddress->getCity(),
            'KBundesland'      => (string)$billingAddress->getCountryZone()->getName(),
            'KTelefon'         => (string)$this->order->getCustomerTelephone(),
            'Kfax'             => '',
            'Kemail'           => (string)$this->order->getCustomerEmail(),
            'KLand'            => $kLand,
            'Lieferanschrift'  => '0',
            'UsStID'           => (string)$this->order->getVatIdNumber(),
            'VID'              => (string)$this->order->getOrderId(),
            'CheckVID'         => '1',
            'Haendler'         => $isB2B ? '1' : '0',
            'Artikelerkennung' => $artikelErkennung,
            'Versandkosten'    => '',
            'Kommentar'        => (string)$this->order->getComment(),
            //'VMemo'            => (string)$this->order->getComment(),
            'Bestandart'       => 'shop',
            'NoFeedback'       => self::FEEDBACKDATUM,
            'SoldCurrency'     => $this->order->getCurrencyCode()->getCode(),
            'BuyDate'          => $this->order->getPurchaseDateTime()->format('d.m.Y H:i:s'),
        ];
        
        if (!empty($this->partnerToken) && !empty($this->accountToken)) {
            $data['PartnerToken'] = $this->partnerToken;
            $data['UserToken']    = $this->accountToken;
        } else {
            $data['PartnerID']   = $this->partnerID;
            $data['PartnerPass'] = $this->partnerPass;
            $data['UserID']      = $this->userID;
        }
        
        $deliveryStreet      = (string)$deliveryAddress->getStreet();
        $deliveryHouseNumber = (string)$deliveryAddress->getHouseNumber();
        if (!empty($deliveryHouseNumber)) {
            $deliveryStreet .= ' ' . $deliveryHouseNumber;
        }
        if ($billingAddress != $deliveryAddress) {
            $kLLand              = CountryCodesMapper::getLicensePlateCodeForIso2Code((string)$deliveryAddress->getCountry()
                ->getIso2()) ?? 'D';
            $deliveryAddressData = [
                'Lieferanschrift' => 1,
                'KLFirma'         => (string)$deliveryAddress->getCompany(),
                'KLVorname'       => (string)$deliveryAddress->getFirstname(),
                'KLNachname'      => (string)$deliveryAddress->getLastname(),
                'KLStrasse'       => $deliveryStreet,
                'KLStrasse2'      => (string)$deliveryAddress->getAdditionalAddressInfo(),
                'KLPLZ'           => (string)$deliveryAddress->getPostcode(),
                'KLOrt'           => (string)$deliveryAddress->getCity(),
                'KBundesland'     => (string)$deliveryAddress->getCountryZone()->getName(),
                'KLLand'          => $kLLand,
            ];
            $data                = array_merge($data, $deliveryAddressData);
        }
        
        $artikelPos        = 0;
        $itemsTaxSum       = 0.0;
        $itemsSum          = 0.0;
        $unRoundedItemsSum = 0.0;
        $paymentTotal      = 0.0;
        $afterbuyTotal     = 0.0;
        /**
         * @var int       $idx
         * @var OrderItem $orderItem
         */
        foreach ($this->order->getOrderItems() as $idx => $orderItem) {
            $artikelPos++;
            
            $this->extendRootIdAndProductNumber($data, $orderItem, $productSyncType, $artikelPos);
            $productsId = (int)$orderItem->getAddonValue(new StringType('productId'));
            
            $itemTaxRate        = $orderItem->getTax();
            $unRoundedItemPrice = $itemPrice = $orderItem->getPrice();
            if ($mustAddTax) {
                if ($separateTax) {
                    $itemTaxRate = 0;
                } else {
                    $itemsTaxSum += $itemPrice * ($orderItem->getTax() / 100) * $orderItem->getQuantity();
                    //$this->logger->debug('orderItemTax: ' . $orderItem->getTax());
                    $itemPrice          *= ($orderItem->getTax() + 100) / 100;
                    $unRoundedItemPrice = $itemPrice;
                }
                $itemPrice = round($itemPrice, 2);
            }
            $data['Artikelname_' . $artikelPos]   = $orderItem->getName();
            $data['ArtikelEPreis_' . $artikelPos] = $this->convertPrice($itemPrice);
            $data['ArtikelMwst_' . $artikelPos]   = $itemTaxRate;
            $data['ArtikelMenge_' . $artikelPos]  = (string)$orderItem->getQuantity();
            $data['ArtikelLink_' . $artikelPos]   = $this->getProductUrl($productsId, $orderItem->getName());
            $itemsSum                             += $itemPrice * $orderItem->getQuantity();
            $unRoundedItemsSum                    += $unRoundedItemPrice * $orderItem->getQuantity();
            
            // refactor attribute usage
            $attributesCollection = $orderItem->getAttributes();
            if ($attributesCollection->isEmpty() !== true) {
                $attributes = [];
                /** @var StoredOrderItemAttribute $attribute */
                foreach ($attributesCollection->getIterator() as $attribute) {
                    $attributes[] = $attribute->getName() . ':' . $attribute->getValue();
                    if ($attribute instanceof OrderItemAttribute) {
                        $attrModel = $this->getAttributeModel($productsId, $attribute);
                        if ($attrModel !== '') {
                            $data['Artikelnr_' . $artikelPos] = $attrModel;
                        }
                    }
                }
                $data['Attribute_' . $artikelPos] = implode('|', $attributes);
            }
        }
        
        $shippingTaxClassId = 0;
        $shippingModule     = $this->order->getShippingType()->getModule();
        if (!empty($shippingModule)) {
            [$shippingClass, $shippingMethod] = explode('_', $shippingModule);
            $shippingTaxClassConfigKey = 'MODULE_SHIPPING_' . strtoupper($shippingClass) . '_TAX_CLASS';
            if (defined($shippingTaxClassConfigKey)) {
                $shippingTaxClassId = (int)constant($shippingTaxClassConfigKey);
            }
        }
        
        $taxTotal = 0.0;
        /** @var $orderTotal StoredOrderTotal */
        foreach ($this->order->getOrderTotals() as $orderTotal) {
            if ($orderTotal->getClass() === 'ot_tax') {
                $taxTotal += $orderTotal->getValue();
            }
        }
        
        $subTotal      = 0.0;
        $afterbuyTotal = $itemsSum;
        
        /** @var $orderTotal StoredOrderTotal */
        foreach ($this->order->getOrderTotals() as $orderTotal) {
            if ($orderTotal->getClass() === 'ot_total') {
                $paymentTotal = $orderTotal->getValue();
            }
            if ($orderTotal->getClass() === 'ot_subtotal') {
                $subTotal = $orderTotal->getValue();
            }
            if ($separateTax && $mustAddTax && $orderTotal->getClass() === 'ot_tax') {
                $artikelPos++;
                $data['Artikelnr_' . $artikelPos]     = 0;
                $data['Artikelname_' . $artikelPos]   = $orderTotal->getTitle();
                $data['ArtikelEPreis_' . $artikelPos] = $this->convertPrice($orderTotal->getValue());
                $data['ArtikelMwst_' . $artikelPos]   = 0;
                $data['ArtikelMenge_' . $artikelPos]  = 1;
                $afterbuyTotal                        += $orderTotal->getValue();
            }
            if (in_array($orderTotal->getClass(), $this->orderTotalIgnore)) {
                continue;
            }
            if ($orderTotal->getClass() === 'ot_shipping') {
                $shippingCost = $orderTotal->getValue();
                if (!$separateTax && $mustAddTax && $shippingTaxClassId > 0) {
                    $shippingTaxRate = $this->getTaxRate($shippingTaxClassId,
                                                         $this->order->getBillingAddress()->getCountry(),
                                                         $this->order->getBillingAddress()->getCountryZone());
                    $shippingCost    *= ($shippingTaxRate + 100) / 100;
                }
                $data['Versandkosten'] = $this->convertPrice($shippingCost);
                $afterbuyTotal         += $shippingCost;
            } elseif (array_key_exists($orderTotal->getClass(), $this->orderTotalConfiguration)) {
                $separateTaxDiscount = false;
                $otConfiguration     = $this->orderTotalConfiguration[$orderTotal->getClass()];
                $artikelEPreis       = $otConfiguration['factor'] * $orderTotal->getValue();
                
                if ($mustAddTax && $subTotal > 0 && $orderTotal->getClass() === 'ot_discount') {
                    $discountRate     = $orderTotal->getValue() / $subTotal;
                    $taxDiscountValue = round($itemsTaxSum, 2) * $discountRate;
                    if (!$separateTaxDiscount) {
                        $artikelEPreis += $taxDiscountValue;
                    }
                }
                
                if ($mustAddTax && !$separateTax && $orderTotal->getClass() === 'ot_payment') {
                    $discountRate         = (float)explode('%', $orderTotal->getTitle())[0];
                    $undiscountedTaxTotal = $taxTotal / (1 - ($discountRate / 100));
                    $taxDiscount          = $undiscountedTaxTotal - $taxTotal;
                    $artikelEPreis        -= $taxDiscount;
                }
                
                $artikelPos++;
                $data['Artikelnr_' . $artikelPos]     = $otConfiguration['Artikelnr'];
                $data['Artikelname_' . $artikelPos]   = $orderTotal->getTitle();
                $data['ArtikelEPreis_' . $artikelPos] = $this->convertPrice($artikelEPreis);
                $data['ArtikelMwst_' . $artikelPos]   = $this->convertPrice($otConfiguration['ArtikelMwst']);
                $data['ArtikelMenge_' . $artikelPos]  = '1';
                $afterbuyTotal                        += $artikelEPreis;
                
                if ($separateTaxDiscount && $mustAddTax && $subTotal > 0 && $orderTotal->getClass() === 'ot_discount') {
                    $artikelPos++;
                    $data['Artikelnr_' . $artikelPos]     = $otConfiguration['Artikelnr'];
                    $data['Artikelname_' . $artikelPos]   = $orderTotal->getTitle() . ' (MWSt.)';
                    $data['ArtikelEPreis_' . $artikelPos] = $artikelEPreis;
                    $data['ArtikelMwst_' . $artikelPos]   = $this->convertPrice($otConfiguration['ArtikelMwst']);
                    $data['ArtikelMenge_' . $artikelPos]  = '1';
                    $afterbuyTotal                        += $artikelEPreis;
                }
            } else {
                $artikelPos++;
                $data['Artikelnr_' . $artikelPos]     = static::GENERIC_OT_ARTNR;
                $data['Artikelname_' . $artikelPos]   = $orderTotal->getTitle();
                $data['ArtikelEPreis_' . $artikelPos] = $this->convertPrice($orderTotal->getValue());
                $data['ArtikelMwst_' . $artikelPos]   = 0;
                $data['ArtikelMenge_' . $artikelPos]  = '1';
                $afterbuyTotal                        += $artikelEPreis;
            }
        }
        $afterbuyTotal = round($afterbuyTotal, 2);
        
        if ((bool)$this->configuration->get('use_correctional_items') === true) {
            if (abs($paymentTotal - $afterbuyTotal) > 0) {
                $artikelPos++;
                $data['Artikelnr_' . $artikelPos]     = 0;
                $data['Artikelname_' . $artikelPos]   = 'Korrekturposten';
                $data['ArtikelEPreis_' . $artikelPos] = $this->convertPrice($paymentTotal - $afterbuyTotal);
                $data['ArtikelMwst_' . $artikelPos]   = 0;
                $data['ArtikelMenge_' . $artikelPos]  = 1;
            }
        }
        
        $data['PosAnz'] = $artikelPos;
        if ($this->configuration->get('send_shipping_info') !== 'never') {
            $data['Versandart'] = preg_replace('/ \(.*?\).*/', '', $this->order->getShippingType()->getTitle());
        }
        $data['NoVersandCalc'] = self::VERSANDERMITTLUNG_AB;
        $data['VID']           = $this->order->getOrderId();
        $afterbuyPayment       = $this->mapPayment($this->order->getPaymentType()->getModule());
        $data['Zahlart']       = $afterbuyPayment['name'];
        if (!empty($afterbuyPayment['id'])) {
            $data['ZFunktionsID'] = $afterbuyPayment['id'];
        }
        if ($this->configuration->get('order_status_paid') === '-1' || $this->orderIsPaid() === true) {
            $data['SetPay'] = 1;
        }
        $this->checkPaypalHubPayment($data);
        
        //$data['debug'] = $this->order->getOrderTotals();
        
        $dataPreparedEvent = AfterbuyOrderDataPrepared::create($data);
        $this->eventDispatcher->dispatch($dataPreparedEvent);
        $data = $dataPreparedEvent->getOrderData();
        
        return $data;
    }
    
    
    /**
     * @param array $data
     *
     * @return void
     */
    private function checkPaypalHubPayment(array &$data): void
    {
        $paymentModule = $this->order->getPaymentType()->getModule();
        $providerClass = implode('', array_map('ucfirst', explode('_', $paymentModule))) . 'PaymentDetailsProvider';
        
        if ($providerClass === 'PayPal2HubPaymentDetailsProvider') {
            $paymentProvider = new PayPal2HubPaymentDetailsProvider();
            $details         = $paymentProvider->getDetails(new IdType($this->orderId));
            $this->checkPaypalHubDetails($details, $data);
        }
    }
    
    
    /**
     * @param array $details
     * @param array $data
     *
     * @return void
     */
    private function checkPaypalHubDetails(array $details, array &$data): void
    {
        if (isset($details['hubdetails']['order'])) { // payment done via PayPal v2 API
            $firstPurchaseUnit = array_shift($details['hubdetails']['order']['purchase_units']);
            if (!empty($firstPurchaseUnit['payments'])) {
                $paymentType  = array_keys($firstPurchaseUnit['payments'])[0];
                $firstPayment = array_shift($firstPurchaseUnit['payments'][$paymentType]);
                if (in_array($firstPayment['status'], ['COMPLETED', 'PARTIALLY_REFUNDED', 'REFUNDED'], true)) {
                    $transactionId                = $firstPayment['id'];
                    $data['SetPay']               = 1;
                    $data['PaymentStatus']        = 'PayPal';
                    $data['PaymentTransactionId'] = $transactionId;
                }
            }
        }
        
        if (isset($details['hubdetails']['payment'])) { // payment done via old v1 API (PLUS)
            if (!empty($details['hubdetails']['payment']['transactions'])) {
                $firstTransaction = array_shift($details['hubdetails']['payment']['transactions']);
                if (!empty($firstTransaction['related_resources'])) {
                    $firstResource     = array_shift($firstTransaction['related_resources']);
                    $firstResourceType = array_keys($firstResource)[0];
                    if (in_array($firstResource[$firstResourceType]['state'],
                                 ['completed', 'partially_refunded', 'refunded'],
                                 true)) {
                        $transactionId                = $firstResource[$firstResourceType]['id'];
                        $data['SetPay']               = 1;
                        $data['PaymentStatus']        = 'PayPal';
                        $data['PaymentTransactionId'] = $transactionId;
                    }
                }
            }
        }
    }
    
    
    /**
     * Extends $data by adding the 'ArtikelStammID_$pos', 'Artikelnr_$pos' and 'AlternArtikelNr1_$pos' fields.
     *
     * @param array           $data
     * @param StoredOrderItem $orderItem
     * @param string          $productSyncType
     * @param int             $pos
     *
     * @return void
     */
    private function extendRootIdAndProductNumber(
        array           &$data,
        StoredOrderItem $orderItem,
        string          $productSyncType,
        int             $pos
    ): void {
        $productsId                    = (int)$orderItem->getAddonValue(new StringType('productId'));
        $combiId                       = $this->getCombiId($orderItem);
        $combiIdSuffix                 = empty($combiId) ? '' : "x{$combiId}";
        $productModel                  = $orderItem->getProductModel();
        $data["AlternArtikelNr1_$pos"] = $productModel;
        
        $productModelDigitsOnly = $this->removeNonDigits($productModel);
        if (empty($productModelDigitsOnly)) {
            $productModelDigitsOnly = $productsId;
        }
        
        if ($productSyncType === 'pid_anr') {
            $data["ArtikelStammID_$pos"]   = $productsId;
            $data["AlternArtikelNr1_$pos"] = $productsId . $combiIdSuffix;
            $data["Artikelnr_$pos"]        = $productsId;
        }
        if ($productSyncType === 'model_anr') {
            if (!empty($combiId) && strpos($productModel, '-') !== false) {
                $modelParts                  = explode('-', $productModel, 2);
                $data["ArtikelStammID_$pos"] = (string)(int)$modelParts[1];
            } else {
                $data["ArtikelStammID_$pos"] = $productModelDigitsOnly;
            }
            $data["Artikelnr_$pos"] = $productModelDigitsOnly;
        }
        if ($productSyncType === 'pid_ean') {
            $data["ArtikelStammID_$pos"]   = $productsId . $combiIdSuffix;
            $data["AlternArtikelNr1_$pos"] = $productsId . $combiIdSuffix;
            $data["Artikelnr_$pos"]        = $productsId;
        }
        if ($productSyncType === 'model_ean') {
            $data["ArtikelStammID_$pos"]   = $productModel;
            $data["AlternArtikelNr1_$pos"] = $productsId . $combiIdSuffix;
            $data["Artikelnr_$pos"]        = $productsId;
        }
        if ($productSyncType === 'model_prodid') {
            if (!empty($combiId) && strpos($productModel, '-') !== false) {
                $modelParts                  = explode('-', $productModel, 2);
                $data["ArtikelStammID_$pos"] = $this->removeNonDigits($modelParts[1]);
            } else {
                $data["ArtikelStammID_$pos"] = $this->removeNonDigits($productModel);
            }
            $data["AlternArtikelNr1_$pos"] = $productsId . $combiIdSuffix;
            $data["Artikelnr_$pos"]        = $productsId;
        }
    }
    
    
    /**
     * Returns the product combi id of the stored order item or null if the item does not represent a variant.
     *
     * @param StoredOrderItem $orderItem
     *
     * @return int|null
     */
    private function getCombiId(StoredOrderItem $orderItem): ?int
    {
        /** @var StoredOrderItemProperty|StoredOrderItemAttribute $attribute */
        foreach ($orderItem->getAttributes() as $attribute) {
            if ($attribute instanceof StoredOrderItemProperty) {
                return $attribute->getCombisId();
            }
        }
        
        return null;
    }
    
    
    /**
     * Removes all non-digits from the given string.
     *
     * @param string $string
     *
     * @return string
     */
    private function removeNonDigits(string $string): string
    {
        return preg_replace('/\D+/', '', $string);
    }
    
    
    /**
     * @param                              $taxClassId
     * @param CustomerCountryInterface     $country
     * @param CustomerCountryZoneInterface $zone
     *
     * @return float
     */
    protected function getTaxRate(
        $taxClassId,
        CustomerCountryInterface $country,
        CustomerCountryZoneInterface $zone
    ): float {
        $db = StaticGXCoreLoader::getDatabaseQueryBuilder();
        
        $taxRateQuery = $db->query("SELECT SUM(`tax_rate`) AS `tax_rate`
					FROM
						`tax_rates` `tr`
					LEFT JOIN `zones_to_geo_zones` `za` ON (`tr`.`tax_zone_id` = `za`.`geo_zone_id`)
					LEFT JOIN `geo_zones` `tz` ON (`tz`.`geo_zone_id` = `tr`.`tax_zone_id`)
					WHERE
						(`za`.`zone_country_id` IS NULL OR
							`za`.`zone_country_id` = '0' OR
							`za`.`zone_country_id` = ?) AND
						(`za`.`zone_id` IS NULL OR
							`za`.`zone_id` = '0' OR
							`za`.`zone_id` = ?) AND
						`tr`.`tax_class_id` = ?
					GROUP BY `tr`.`tax_priority`",
                                   [$country->getId(), $zone->getId(), (int)$taxClassId]);
        
        $multiplier = 1.0;
        foreach ($taxRateQuery->result_array() as $row) {
            $multiplier *= ($row['tax_rate'] + 100) / 100;
        }
        $taxRate = ($multiplier - 1.0) * 100;
        
        return $taxRate;
    }
    
    
    /**
     * @param int    $productsId
     * @param string $productName
     *
     * @return string
     */
    protected function getProductUrl(int $productsId, string $productName): string
    {
        if ($GLOBALS['gmSEOBoost']->boost_products) {
            $productUrl = xtc_href_link($GLOBALS['gmSEOBoost']->get_boosted_product_url($productsId, $productName));
        } else {
            $productUrl = xtc_href_link('product_info.php', xtc_product_link($productsId, $productName));
        }
        
        if (strlen($productUrl) > 255) {
            $productUrl = xtc_href_link('product_info.php', 'info=p' . $productsId);
        }
        
        return $productUrl;
    }
    
    
    /**
     * @param $data
     *
     * @return string|null KundenNr for newly transferred order, null for already transferred order
     * @throws AfterbuyException if sending data fails
     */
    protected function sendData($data): ?string
    {
        $requestData = http_build_query($data);
        $this->logger->notice("Request data:\n" . $requestData);
        $ch          = curl_init();
        $curlOptions = [
            CURLOPT_URL            => self::AFTERBUY_URL,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $requestData,
            CURLOPT_TIMEOUT        => self::REQUEST_TIMEOUT,
        ];
        curl_setopt_array($ch, $curlOptions);
        $response  = curl_exec($ch);
        $curlInfo  = curl_getinfo($ch);
        $curlErrno = curl_errno($ch);
        $curlError = curl_error($ch);
        curl_close($ch);
        $this->logger->notice("Received response:\n" . $response);
        if ($curlErrno !== CURLE_OK) {
            throw new AfterbuyException(sprintf('Request failed - %d/%s', $curlErrno, $curlError));
        }
        $xmlResponse = simplexml_load_string($response);
        if ($xmlResponse === false) {
            throw new AfterbuyException("Response from Afterbuy could not be parsed:\n===\n" . $response . "\n===\n");
        }
        $this->mapAfterbuyToShopOrderId($response);
        
        if ((string)$xmlResponse->success === '1') {
            return (string)$xmlResponse->data->KundenNr;
        }
        
        $afterbuyError = (string)$xmlResponse->errorlist->error;
        if ($this->strContains(strtolower($afterbuyError), 'bestellung wurde bereits erfasst')) {
            return null;
        }
        
        $message = "Afterbuy Error: $afterbuyError";
        throw new AfterbuyException($message);
    }
    
    
    /**
     * @param int    $orderId
     * @param string $kundenNr
     *
     * @return void
     */
    private function markOrderAsTransferred(int $orderId, string $kundenNr): void
    {
        $db = StaticGXCoreLoader::getDatabaseQueryBuilder();
        $db->set('afterbuy_success', '1')
            ->set('afterbuy_id', $kundenNr)
            ->where('orders_id', $this->orderId)
            ->update('orders');
    }
    
    
    /**
     * Checks if $haystack contains $needle.
     * Copied for convenience from Symfony polyfill `src/vendor/symfony/polyfill-php80/Php80.php`.
     *
     * @param string $haystack
     * @param string $needle
     *
     * @return bool
     */
    private function strContains(string $haystack, string $needle): bool
    {
        return '' === $needle || false !== strpos($haystack, $needle);
    }
    
    
    /**
     * Updates the order status with given information using the `OrderWriteService::updateOrderStatus`.
     *
     * @param int    $orderId
     * @param int    $orderStatusId
     * @param string $comment
     *
     * @return void
     */
    private function updateOrderStatus(int $orderId, int $orderStatusId, string $comment): void
    {
        /** @var OrderWriteService $orderWriteService */
        $orderWriteService = StaticGXCoreLoader::getService('OrderWrite');
        if (method_exists($orderWriteService, 'updateOrderStatusAfterProcessOrder')) {
            $orderWriteService->updateOrderStatusAfterProcessOrder(new IdType($orderId),
                                                                   new IdType($orderStatusId),
                                                                   new StringType($comment),
                                                                   new BoolType(false));
        }
    }
    
    
    /**
     * @param int    $orderId
     * @param string $comment
     *
     * @return void
     */
    private function addOrderStatusHistoryEntry(int $orderId, string $comment): void
    {
        /** @var OrderWriteService $orderWriteService */
        $orderWriteService = StaticGXCoreLoader::getService('OrderWrite');
        $orderWriteService->addOrderStatusHistoryEntry(new \IdType($orderId),
                                                       new \StringType($comment),
                                                       new \IdType(0));
    }
    
    
    /**
     * Maps Afterbuy order ids to shop order ids.
     *
     * This method takes the whole XML-Response of the Afterbuy Shop-API response, parse it and checks
     * if the Afterbuy and Shop order ids are available. If so, the mapped ids are stored in the
     * 'afterbuy_orders' database table.
     *
     * @param string $xmlResponse
     */
    private function mapAfterbuyToShopOrderId(string $xmlResponse): void
    {
        $container = LegacyDependencyContainer::getInstance();
        try {
            $service = $container->get(AfterbuyOrderIdMappingService::class);
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            $errorType = gettype($e);
            $message   = "Error ($errorType): Failed to get the AfterbuyShopApiService to map Afterbuy- and Shop order ids.\n";
            $context   = [
                'errorMessage' => $e->getMessage(),
                'errorCode'    => $e->getCode(),
                'errorFile'    => $e->getFile(),
                'errorLine'    => $e->getLine(),
            ];
            $this->logger->notice($message, $context);
            
            return;
        }
        
        try {
            $service->mapAfterbuyOrderIdToShopOrderId($xmlResponse);
        } catch (AfterbuyOrderIdAlreadyMappedException $e) {
            return; // We do nothing in this case, because the ids are already mapped
        } catch (AfterbuyOrderIdMappingException $e) {
            $message = "Failed to map Afterbuy order id to shop order id.";
            $context = [
                'shopOrderId'     => $e->orderId(),
                'afterbuyOrderId' => $e->afterbuyOrderId(),
                'xmlResponse'     => $e->xmlResponse(),
            ];
            
            $this->logger->warning($message, $context);
        } catch (Throwable $t) {
            $message = "Failed to map Afterbuy order id to shop order id.";
            $context = [
                'exception'     => $this->getContextForThrowable($t),
                'exceptionType' => get_class($t),
                'xmlResponse'   => $xmlResponse,
            ];
            
            $this->logger->warning($message, $context);
        }
    }
    
    
    /**
     * @param $price
     * @param $currencyFactor
     *
     * @return string
     */
    protected function convertPrice($price, $currencyFactor = 1.0): string
    {
        $convertedPrice = (float)$price / $currencyFactor;
        $convertedPrice = number_format($convertedPrice, 4, ',', '');
        
        return $convertedPrice;
    }
    
    
    /**
     * @param                          $productsId
     * @param StoredOrderItemAttribute $orderItemAttribute
     *
     * @return array|string|string[]|null
     */
    protected function getAttributeModel($productsId, StoredOrderItemAttribute $orderItemAttribute)
    {
        $db           = StaticGXCoreLoader::getDatabaseQueryBuilder();
        $attrQueryRow = $db->select('attributes_model')
            ->from('products_attributes')
            ->where('products_id', $productsId)
            ->where('options_id', $orderItemAttribute->getOptionId())
            ->where('options_values_id', $orderItemAttribute->getOptionValueId())
            ->get()
            ->row();
        if ($attrQueryRow !== null) {
            $attributeModel = $attrQueryRow->attributes_model;
            $attributeModel = preg_replace('/\D+/', '', $attributeModel);
        } else {
            $attributeModel = '';
        }
        
        return $attributeModel;
    }
    
    
    /**
     * @param $paymentCode
     *
     * @return mixed
     */
    protected function mapPayment($paymentCode)
    {
        if (array_key_exists($paymentCode, $this->paymentMapping)) {
            $afterbuyPayment = $this->paymentMapping[$paymentCode];
        } else {
            $afterbuyPayment = $this->paymentMapping['default'];
        }
        
        return $afterbuyPayment;
    }
    
    
    /**
     * Checks if the order is to be considered paid in full.
     *
     * An order is paid if the order status configured as order_status_paid is found in the order’s status history.
     *
     * @return bool
     */
    public function orderIsPaid(): bool
    {
        $container = LegacyDependencyContainer::getInstance();
        try {
            $service = $container->get(AfterbuyCheckPaidStatusService::class);
            
            return $service->getPaidStatus(new OrderId($this->orderId))->isPaid();
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            $expectedClass = AfterbuyCheckPaidStatusService::class;
            $message       = "$expectedClass is not registered in the LegacyDependencyContainer.\nError: {$e->getMessage()}";
            $context       = array_merge($this->getContextForClassMember(), $this->getContextForThrowable($e));
            $this->logger->warning($message, $context);
        } catch (AfterbuyOrderStatusPaidException $e) {
            $message = "Failed to get the information if the order status is marked as paid.";
            $context = array_merge($this->getContextForClassMember(), $this->getContextForThrowable($e));
            $this->logger->notice($message, $context);
        }
        
        $paidOrderStatusValue = $this->configuration->get('order_status_paid');
        $paidOrderStatus      = explode(',', $paidOrderStatusValue);
        $paidOrderStatus      = array_map(fn(string $element): int => (int)$element, $paidOrderStatus);
        
        $isPaid = false;
        /** @var OrderStatusHistoryListItem $statusHistoryItem */
        foreach ($this->order->getStatusHistory() as $statusHistoryItem) {
            if (in_array($statusHistoryItem->getOrderStatusId(), $paidOrderStatus, true)) {
                $isPaid = true;
                break;
            }
        }
        
        return $isPaid;
    }
}
