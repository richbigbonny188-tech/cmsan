<?php
/* --------------------------------------------------------------
   KlarnaHubConfiguration.inc.php 2023-01-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

use \HubPublic\ValueObjects\HubClientKey;

/**
 * Class KlarnaHubConfiguration
 *
 * Prepares KlarnaHub related configuration for JavaScript modules.
 *
 * @package    GXModules
 * @subpackage GambioHub
 */
class KlarnaHubConfiguration
{
	/**
	 * @var string
	 */
	const DEFAULT_ORDER_STATUS_SHIPPED = '3';
	
	/**
	 * @var LanguageTextManager
	 */
	protected $languageTextManager;
	
	/**
	 * @var Countries
	 */
	protected $countries;
	
	/**
	 * @var OrderReadServiceInterface
	 */
	protected $orderReadService;
	
	/**
	 * @var OrderJsonSerializer
	 */
	protected $orderJsonSerializer;
	
	/**
	 * @var KlarnaHubFactory
	 */
	protected $klarnaHubFactory;
	
	
	/**
	 * KlarnaHubConfiguration constructor.
	 *
	 * @param LanguageTextManager       $languageTextManager Used for reading KlarnaHub translations.
	 * @param Countries                 $countries           Read the available countries.
	 * @param OrderReadServiceInterface $orderReadService    Reads order record.
	 * @param OrderJsonSerializer       $orderJsonSerializer Serializes order record to assosiative array.
	 * @param KlarnaHubFactory          $klarnaHubFactory    Klarna Hub factory.
	 */
	public function __construct(LanguageTextManager $languageTextManager,
	                            Countries $countries,
	                            OrderReadServiceInterface $orderReadService,
	                            OrderJsonSerializer $orderJsonSerializer,
	                            KlarnaHubFactory $klarnaHubFactory)
	{
		$this->languageTextManager = $languageTextManager;
		$this->countries           = $countries;
		$this->orderReadService    = $orderReadService;
		$this->orderJsonSerializer = $orderJsonSerializer;
		$this->klarnaHubFactory    = $klarnaHubFactory;
	}
	
	
	/**
	 * Returns the configuration as an associative array.
	 *
	 * @param \HubPublic\ValueObjects\HubClientKey|null $clientKey   Hub client key.
	 * @param NonEmptyStringType|null                   $moduleCode  Hub module code of the order.
	 * @param NonEmptyStringType|null                   $orderNumber Shop order number.
	 *
	 * @return array Returns the configuration array.
	 */
	public function asArray(HubClientKey $clientKey = null,
	                        NonEmptyStringType $moduleCode = null,
	                        NonEmptyStringType $orderNumber = null)
	{
		$serializedOrder = null;
		$klarnaOrder     = null;
		
		if($clientKey && $orderNumber)
		{
			$orderId                 = new IdType($orderNumber->asString());
			$order                   = $this->orderReadService->getOrderById($orderId);
			$serializedOrder         = $this->orderJsonSerializer->serialize($order, false);
            
            $totalModulesClasses = [];
            foreach ($serializedOrder['totals'] as $orderTotal) {
                $totalModulesClasses[] = $orderTotal['class'];
            }
            $taxAdded = in_array('ot_subtotal_no_tax', $totalModulesClasses, true)
                        && in_array('ot_tax', $totalModulesClasses, true);
            if ($taxAdded) {
                foreach ($serializedOrder['items'] as $itemIndex => $item) {
                    $grossPrice = $serializedOrder['items'][$itemIndex]['price'] * (1 + ($item['tax'] / 100));
                    $serializedOrder['items'][$itemIndex]['price'] = (float)number_format($grossPrice, 2);
                    $grossFinalPrice = $serializedOrder['items'][$itemIndex]['finalPrice'] * (1 + ($item['tax'] / 100));
                    $serializedOrder['items'][$itemIndex]['finalPrice'] = (float)number_format($grossFinalPrice, 2);
                }
                foreach ($serializedOrder['totals'] as $totalIndex => $total) {
                    if ($total['class'] === 'ot_shipping') {
                        [$shippingModuleClass, $shippingModuleMethod] = explode('_', $serializedOrder['shippingType']['module']);
                        $shippingModuleTaxClassConstantName = 'MODULE_SHIPPING_' . strtoupper($shippingModuleClass) . '_TAX_CLASS';
                        if (defined($shippingModuleTaxClassConstantName)) {
                            $taxRate = xtc_get_tax_rate((int)constant($shippingModuleTaxClassConstantName),
                                                        $serializedOrder['addresses']['delivery']['countryId'],
                                                        $serializedOrder['addresses']['delivery']['zoneId']);
                            $grossValue = $serializedOrder['totals'][$totalIndex]['value'] * (1 + ($taxRate / 100));
                            $serializedOrder['totals'][$totalIndex]['value'] = (float)number_format($grossValue, 2);
                        }
                    }
                }
            }
            
			$klarnaHubCallbackClient = $this->klarnaHubFactory->createCallbackClient($moduleCode, $orderNumber);
			try
			{
				$klarnaOrder = $klarnaHubCallbackClient->getKlarnaOrder();
			}
			catch(Exception $exc)
			{
				// Cannot fetch the Klarna order, include default values. 
				$klarnaOrder = ['order_lines' => []];
			}
		}
		
		$configuration = [
			'appUrl'             => DIR_WS_CATALOG,
			'debug'              => file_exists(DIR_FS_CATALOG . '.dev-environment'),
			'clientKey'          => $clientKey ? $clientKey->asString() : null,
			'moduleCode'         => $moduleCode ? $moduleCode->asString() : null,
			'orderNumber'        => $orderNumber ? $orderNumber->asString() : null,
			'order'              => $serializedOrder,
			'orderStatusShipped' => gm_get_conf('GAMBIO_HUB_REMOTE_CONFIG_'
			                                    . ($moduleCode ? strtoupper($moduleCode->asString()) : 'KLARNAHUB')
			                                    . '_ORDERSTATUSSHIPPED') ? : self::DEFAULT_ORDER_STATUS_SHIPPED,
			'klarnaOrder'        => $klarnaOrder,
			'lang'               => $this->languageTextManager->get_section_array('gambio_hub_klarna_hub'),
			'countries'          => $this->countries->get_all_countries(),
			'giftSystem'         => defined('ACTIVATE_GIFT_SYSTEM') ? filter_var(ACTIVATE_GIFT_SYSTEM,
			                                                                     FILTER_VALIDATE_BOOLEAN) : false
		];
		
		return $configuration;
	}
}
