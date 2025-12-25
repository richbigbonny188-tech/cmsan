<?php
/* --------------------------------------------------------------
	InternetMarkeController.inc.php 2023-04-26
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2023 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

defined('GM_HTTP_SERVER') or define('GM_HTTP_SERVER', HTTP_SERVER);

class InternetMarkeController extends AdminHttpViewController
{
	/**
	 * @var GXCoreLoaderSettingsInterface
	 */
	private $settings;

	/**
	 * @var GXCoreLoaderInterface
	 */
	private $loader;

	/**
	 * @var CI_DB_query_builder
	 */
	protected $db;

	protected $internetMarkeText;
	protected $internetMarkeConfigurationStorage;
	protected $internetMarkeLogger;

	public function __construct(HttpContextReaderInterface $httpContextReader,
	                            HttpResponseProcessorInterface $httpResponseProcessor,
	                            ContentViewInterface $contentView)
	{
		parent::__construct($httpContextReader, $httpResponseProcessor, $contentView);
		$this->internetMarkeText                 = MainFactory::create('InternetMarkeText');
		$this->internetMarkeConfigurationStorage = MainFactory::create('InternetMarkeConfigurationStorage');
		$this->internetMarkeLogger               = MainFactory::create('InternetMarkeLogger');
		$gxCoreLoader                            = MainFactory::create('GXCoreLoader', MainFactory::create('GXCoreLoaderSettings'));
		$this->db                                = $gxCoreLoader->getDatabaseQueryBuilder();
	}

	/**
	 * Override "proceed" method of parent and use it for initialization.
	 *
	 * This method must call the parent "proceed" in order to work properly.
	 *
	 * @param HttpContextInterface $httpContext
	 */
	public function proceed(HttpContextInterface $httpContext)
	{
		$this->settings = MainFactory::create('GXCoreLoaderSettings');
		$this->loader   = MainFactory::create('GXCoreLoader', $this->settings);
		// Set the template directory.
		$this->contentView->set_template_dir(DIR_FS_CATALOG . '/GXModules/Gambio/Internetmarke/Admin/Html/');
		// Call the parent "proceed" method.
		parent::proceed($httpContext);
	}

	/**
	 * Run the actionDefault method.
	 */
	public function actionDefault()
	{
		return new RedirectHttpControllerResponse(GM_HTTP_SERVER.DIR_WS_CATALOG);
	}

	public function actionSetSessionCredentials()
	{
		$postDataArray = $this->_getPostDataCollection()->getArray();
		$response      = array(
			'result'        => 'ERROR',
			'orders_id'     => $postDataArray['orders_id'],
			'error_message' => 'no error',
		);

		$this->internetMarkeConfigurationStorage->setForSession('oneclick4app/credentials/email',    $postDataArray['credentials_email']);
		$this->internetMarkeConfigurationStorage->setForSession('oneclick4app/credentials/password', $postDataArray['credentials_password']);
		try
		{
			$onec4a               = new OneClick4Application();
			$authenticateResponse = $onec4a->authenticateUser();
			$response['result']   = 'OK';
			unset($response['error_message']);
		}
		catch(Exception $e)
		{
			$response['error_message'] = $this->internetMarkeText->get_text('error_credentials_invalid');
			$this->internetMarkeConfigurationStorage->setForSession('oneclick4app/credentials/email', '');
			$this->internetMarkeConfigurationStorage->setForSession('oneclick4app/credentials/password', '');
		}
		return new JsonHttpControllerResponse($response);
	}

	public function actionCreateLabelForm()
	{
		$orders_id = (int)$this->_getQueryParameter('orders_id');
		$templateVersion = (int)$this->_getQueryParameter('template_version');
		if($this->internetMarkeConfigurationStorage->credentialsRequired() === true)
		{
			$formdata = array(
				'orders_id'            => $orders_id,
				'credentials_email'    => $this->internetMarkeConfigurationStorage->get('oneclick4app/credentials/email'),
				'credentials_password' => $this->internetMarkeConfigurationStorage->get('oneclick4app/credentials/password'),
			);
			$html = $this->_render('internetmarke_enter_credentials.html', $formdata);
		}
		else
		{
			try
			{
                $onec4a               = new OneClick4Application();
                $authenticateResponse = $onec4a->authenticateUser();
                $contractProducts     = $onec4a->retrieveContractProducts();
                $walletBalanceValue   = (double)$authenticateResponse->walletBalance / 100;
                $walletBalance        = number_format($walletBalanceValue, 2, ',', '');
                $walletBalance        .= '&nbsp;EUR';
                $pageFormats          = $onec4a->retrievePageFormatsList();
                
                $dpProductInfo        = new DPProductInformationService();
				$forceUpdate          = isset($_GET['force_update']);
				$productList          = $dpProductInfo->getPPLProductList($forceUpdate);
				if ((bool)$this->internetMarkeConfigurationStorage->get('oneclick4app/show_contract_products_only') === true) {
				    $productList = array_intersect_key($productList, $contractProducts);
                }
				$favorites = $this->internetMarkeConfigurationStorage->get('oneclick4app/favorite_products');
				if (!empty($favorites)) {
				    $productList = array_intersect_key($productList, array_flip($favorites));
                }
				$minProductPrice      = 99999;
				foreach($productList as $product)
				{
					$minProductPrice = min($minProductPrice, (float)$product->priceDefinition->price->calculatedGrossPrice->value);
				}
				$respectLowWalletBalance = (bool)$this->internetMarkeConfigurationStorage->get('oneclick4app/low_wallet_balance');

				if($walletBalanceValue >= $minProductPrice || $respectLowWalletBalance === false)
				{
					require_once DIR_FS_ADMIN .'includes/classes/order.php';
					$countries       = array();
					$countries_query = $this->db->get_where('countries', array('status' => '1'));
					foreach($countries_query->result() as $country_row)
					{
						$countries[$country_row->countries_id] = array(
							'name' => $country_row->countries_name,
							'iso2' => $country_row->countries_iso_code_2,
							'iso3' => $country_row->countries_iso_code_3,
						);
					}
					$order                = new order($orders_id);
					if(array_key_exists('house_number', $order->delivery) && !empty($order->delivery['house_number']))
					{
						$splitStreet = ['street' => $order->delivery['street_address'], 'house_no' => $order->delivery['house_number']];
					}
					else
					{
						$splitStreet          = $this->splitStreet($order->delivery['street_address']);
					}
					
					$vouchers             = $this->findVouchers($orders_id);
                    
                    $formdata = [
                        'orders_id'               => $orders_id,
                        'order'                   => $order,
                        'walletBalance'           => $walletBalance,
                        'walletBalanceValue'      => $walletBalanceValue,
                        'respectLowWalletBalance' => $respectLowWalletBalance,
                        'minProductPrice'         => $minProductPrice,
                        'countries'               => $countries,
                        'productList'             => $productList,
                        'contractProducts'        => $contractProducts,
                        'pageFormats'             => $pageFormats,
                        'vouchers'                => $vouchers,
                        'delivery_street'         => $splitStreet['street'],
                        'delivery_houseno'        => $splitStreet['house_no'],
                        'prefs'                   => [
                            'productcode'   => $this->internetMarkeConfigurationStorage->get(
                                'oneclick4app/prefs/productcode'
                            ),
                            'voucherlayout' => $this->internetMarkeConfigurationStorage->get(
                                'oneclick4app/prefs/voucherlayout'
                            ),
                            'pageformatid'  => $this->internetMarkeConfigurationStorage->get(
                                'oneclick4app/prefs/pageformatid'
                            ),
                        ],
                        'sender'                  => [
                            'company'   => $this->internetMarkeConfigurationStorage->get('oneclick4app/sender/company'),
                            'firstname' => $this->internetMarkeConfigurationStorage->get(
                                'oneclick4app/sender/firstname'
                            ),
                            'lastname'  => $this->internetMarkeConfigurationStorage->get(
                                'oneclick4app/sender/lastname'
                            ),
                            'street'    => $this->internetMarkeConfigurationStorage->get('oneclick4app/sender/street'),
                            'houseno'   => $this->internetMarkeConfigurationStorage->get('oneclick4app/sender/houseno'),
                            'zip'       => $this->internetMarkeConfigurationStorage->get('oneclick4app/sender/zip'),
                            'city'      => $this->internetMarkeConfigurationStorage->get('oneclick4app/sender/city'),
                            'country'   => $this->internetMarkeConfigurationStorage->get('oneclick4app/sender/country'),
                        ],
                        'receiver'                => [
                            'firstname'    => $order->delivery['firstname'],
                            'lastname'     => $order->delivery['lastname'],
                            'company'      => $order->delivery['company'],
                            'additional'   => empty($order->delivery['additional_address_info']) ? '' : $order->delivery['additional_address_info'],
                            'street'       => $splitStreet['street'],
                            'houseno'      => $splitStreet['house_no'],
                            'zip'          => $order->delivery['postcode'],
                            'city'         => $order->delivery['city'],
                            'country_iso2' => $order->delivery['country_iso_code_2'],
                            'country'      => $order->delivery['country'],
                        ],
                        'wallet_url'              => xtc_href_link('admin.php', 'do=InternetMarke/Wallet'),
                    ];
					
					if($templateVersion == 2)
					{
						$html = $this->_render('internetmarke_form_single_v2.html', $formdata);
					}
					else
					{
						$html = $this->_render('internetmarke_form_single.html', $formdata);
					}
				}
				else
				{
					$formdata = [
						'walletBalanceValue' => $walletBalanceValue,
						'walletBalance'      => $walletBalance,
						'minProductPrice'    => $minProductPrice,
						'wallet_url'         => xtc_href_link('admin.php', 'do=InternetMarke/Wallet'),
					];
					$html = $this->_render('internetmarke_balance_too_low.html', $formdata);
				}

			}
			catch(Exception $e)
			{
				$html = '<p class="error">'.htmlspecialchars($e->getMessage()).'</p>';
			}
		}
		$html = $this->internetMarkeText->replaceLanguagePlaceholders($html);
		$html = preg_replace('_%toslink%(.*)%/toslink%_',
			                 '<a target="_blank" href="' . xtc_href_link('images/AGB_INTERNETMARKE_PORTOKASSE_02.06.2014.pdf', '', 'SSL') . '">$1</a>',
			                 $html);
		return new HttpControllerResponse($html);
	}
    
    
    public function actionCreateLabelFormSubmit()
    {
        $postDataArray = $this->_getPostDataCollection()->getArray();
        $position      = [
            'productCode'   => (string)$postDataArray['productCode'],
            'address'       => [
                'sender'   => [
                    'name'    => [
                        'companyName' => [
                            'company'    => $this->internetMarkeConfigurationStorage->get('oneclick4app/sender/company'),
                            'personName' => [
                                'firstname' => $this->internetMarkeConfigurationStorage->get('oneclick4app/sender/firstname'),
                                'lastname'  => $this->internetMarkeConfigurationStorage->get('oneclick4app/sender/lastname'),
                            ],
                        ],
                    ],
                    'address' => [
                        'street'  => $this->internetMarkeConfigurationStorage->get('oneclick4app/sender/street'),
                        'houseNo' => $this->internetMarkeConfigurationStorage->get('oneclick4app/sender/houseno'),
                        'zip'     => $this->internetMarkeConfigurationStorage->get('oneclick4app/sender/zip'),
                        'city'    => $this->internetMarkeConfigurationStorage->get('oneclick4app/sender/city'),
                        'country' => $this->internetMarkeConfigurationStorage->get('oneclick4app/sender/country'),
                    ],
                ],
                'receiver' => [
                    'name'    => [],
                    'address' => [
                        'street'     => $postDataArray['receiver']['street'],
                        'houseNo'    => $postDataArray['receiver']['houseno'],
                        'zip'        => $postDataArray['receiver']['zip'],
                        'city'       => $postDataArray['receiver']['city'],
                        'country'    => $postDataArray['receiver']['country'],
                        'additional' => isset($postDataArray['receiver']['additional']) ? $postDataArray['receiver']['additional'] : '',
                    ],
                ],
            ],
            'voucherLayout' => $postDataArray['voucherLayout'],
            'position'      => [
                'labelX' => (int)$postDataArray['position_labelx'],
                'labelY' => (int)$postDataArray['position_labely'],
                'page'   => (int)$postDataArray['position_page'],
            ],
        ];
        
        $imageID = $this->internetMarkeConfigurationStorage->get('oneclick4app/prefs/imageid');
        if ($imageID >= 0) {
            $position['imageID'] = (int)$imageID;
        }
        
        if (!empty($postDataArray['receiver']['company'])) {
            $position['address']['receiver']['name']['companyName'] = [
                'company'    => $postDataArray['receiver']['company'],
                'personName' => [
                    'firstname' => $postDataArray['receiver']['firstname'],
                    'lastname'  => $postDataArray['receiver']['lastname'],
                ],
            ];
        } else {
            $position['address']['receiver']['name']['personName'] = [
                'firstname' => $postDataArray['receiver']['firstname'],
                'lastname'  => $postDataArray['receiver']['lastname'],
            ];
        }
        
        $voucherData = [];
        try {
            $onec4a           = new OneClick4Application();
            $contractProducts = $onec4a->retrieveContractProducts();
            if (array_key_exists((int)$position['productCode'], $contractProducts)
                && isset($contractProducts[$position['productCode']]['price'])) {
                $total = $contractProducts[$position['productCode']]['price'];
            } else {
                $dpProductInfo = new DPProductInformationService();
                $total         = (double)$dpProductInfo->getProductCost($position['productCode']);
                $total         = round($total * 100);
            }
            $positions       = [$position];
            $voucherResponse = $onec4a->checkoutShoppingCart($positions,
                                                             $postDataArray['pageFormatID'],
                                                             $total,
                                                             $postDataArray['output'] ?? 'pdf');
            $result          = 'OK';
            $voucherData     = [
                'link'          => $voucherResponse->link,
                'walletBalance' => $voucherResponse->walletBallance,
                'shopOrderId'   => $voucherResponse->shoppingCart->shopOrderId,
                'voucherId'     => $voucherResponse->shoppingCart->voucherList->voucher[0]->voucherId,
                'trackId'       => $voucherResponse->shoppingCart->voucherList->voucher[0]->trackId ?? '',
            ];
            $this->storeVoucherData($postDataArray['orders_id'], $voucherData);
            $this->internetMarkeLogger->notice(sprintf('created voucher with voucherID %s for order %d with shopOrderId %s, tracking %s',
                                                       $voucherData['voucherId'],
                                                       $postDataArray['orders_id'],
                                                       $voucherData['shopOrderId'],
                                                       !empty($voucherData['trackId']) ? $voucherData['trackId'] : 'none'));
            
            $trackingUrl = 'https://www.deutschepost.de/sendung/simpleQueryResult.html?form.sendungsnummer={TRACKING_NUMBER}&form.einlieferungsdatum_tag={VOUCHER_DAY}&form.einlieferungsdatum_monat={VOUCHER_MONTH}&form.einlieferungsdatum_jahr={VOUCHER_YEAR}';
            $trackingUrl = strtr($trackingUrl,
                                 [
                                     '{TRACKING_NUMBER}' => !empty($voucherData['trackId']) ? $voucherData['trackId'] : $voucherData['voucherId'],
                                     '{VOUCHER_DAY}'     => date('d'),
                                     '{VOUCHER_MONTH}'   => date('m'),
                                     '{VOUCHER_YEAR}'    => date('Y'),
                                 ]);
            
            $parcelServiceId = $this->internetMarkeConfigurationStorage->get('oneclick4app/parcelservice_id');
            if ($parcelServiceId > 0) {
                $trackingNumber = $voucherData['voucherId'];
                if (!empty($voucherData['trackId'])) {
                    $trackingNumber = $voucherData['trackId']; //. ' (' . $voucherData['voucherId'] . ')';
                }
                $parcelServiceReader      = MainFactory::create('ParcelServiceReader');
                $parcelTrackingCodeWriter = MainFactory::create('ParcelTrackingCodeWriter');
                $parcelTrackingCodeWriter->insertTrackingUrl($postDataArray['orders_id'],
                                                             $trackingUrl,
                                                             $parcelServiceId,
                                                             $parcelServiceReader,
                                                             $trackingNumber);
            }
            
            $order_status_after_label = $this->internetMarkeConfigurationStorage->get('oneclick4app/order_status_after_label');
            $orderStatusComment       = sprintf("%s (Order ID %s)\n%s",
                                                $this->internetMarkeText->get_text('label_created'),
                                                $voucherResponse->shoppingCart->shopOrderId,
                                                $trackingUrl);
            $notifyCustomer           = (bool)$this->internetMarkeConfigurationStorage->get('oneclick4app/notify_customer')
                                        === true;
            $this->setOrderStatus($postDataArray['orders_id'],
                                  $order_status_after_label,
                                  $orderStatusComment,
                                  $notifyCustomer);
        } catch (Exception $e) {
            $output = '';
            $result = 'ERROR';
            foreach ($e->detail as $exceptionType => $exception) {
                if ($exceptionType === 'ShoppingCartValidationException') {
                    $phraseName        = 'error_' . strtolower((string)$exception->errors->id);
                    $translatedMessage = $this->internetMarkeText->get_text($phraseName);
                    if ($translatedMessage !== $phraseName) {
                        $output .= $translatedMessage;
                    }
                    $output .= "\n\nERROR: " . $e->getMessage();
                    $output .= "\n" . (string)$exception->errors->message;
                }
            }
            $output = $output ? : 'ERROR: ' . $e->getMessage();
        }
        
        $response = [
            'result'        => $result,
            'output'        => $output ?? '',
            'error_message' => $result === 'ERROR' ? $output : '',
            'voucherData'   => $voucherData,
            'orders_id'     => $postDataArray['orders_id'],
        ];
        
        return new JsonHttpControllerResponse($response);
    }
    
    
    public function actionPreviewVoucher()
    {
        $postDataArray = $this->_getPostDataCollection()->getArray();
		$result        = 'ERROR';
		$previewlink   = '';
		$imageID       = $this->internetMarkeConfigurationStorage->get('oneclick4app/prefs/imageid');
		$imageID       = $imageID >= 0 ? (string)$imageID : null;
		$productID     = (string)$postDataArray['productCode'];
		$voucherLayout = (string)$postDataArray['voucherLayout'];

		try
		{
			$onec4a          = new OneClick4Application();
			$previewResponse = $onec4a->retrievePreviewVoucherPNG($productID, $imageID, $voucherLayout);
			$result          = 'OK';
			$previewlink     = (string)$previewResponse->link;
		}
		catch(Exception $e )
		{
			$error_message = $e->getMessage();
		}

		$response = array(
			'result'      => $result,
			'previewlink' => $previewlink,
		);
		if(isset($error_message))
		{
			$response['error_message'] = $error_message;
		}
		return new JsonHttpControllerResponse($response);
	}

	public function actionListVouchers()
	{
		$templateVersion = (int)$this->_getQueryParameter('template_version');
		$orders_id       = $this->_getQueryParameter('orders_id');
		$vouchers        = $this->findVouchers($orders_id);
		$formdata        = array(
			'orders_id' => $orders_id,
			'vouchers'  => $vouchers,
			'debug'     => htmlspecialchars(print_r($vouchers, true)),
		);
		if($templateVersion == 2)
		{
			$html = $this->_render('internetmarke_list_vouchers_v2.html', $formdata);
		}
		else
		{
			$html = $this->_render('internetmarke_list_vouchers.html', $formdata);
		}
		$html = $this->internetMarkeText->replaceLanguagePlaceholders($html);
		return new HttpControllerResponse($html);
	}

	public function findVouchers($orders_id)
	{
		$vouchersQuery = $this->db->get_where('inetmarke_vouchers', array('orders_id' => $orders_id));
		$vouchers      = $vouchersQuery->result();
		return $vouchers;
	}

	/* =================================================================================== */

	protected function storeVoucherData($orders_id, $voucherData)
	{
		$insertData = array(
			'orders_id'     => (int)$orders_id,
			'link'          => (string)$voucherData['link'],
			'walletBalance' => (string)$voucherData['walletBalance'],
			'shopOrderId'   => (string)$voucherData['shopOrderId'],
			'voucherId'     => (string)$voucherData['voucherId'],
			'trackId'       => (string)$voucherData['trackId'],
			//'create_time'   => date('Y-m-d H:i:s'),
		);
		$this->db->insert('inetmarke_vouchers', $insertData);
	}

	protected function splitStreet($street_address)
	{
		$splitStreet = array(
			'street'   => $street_address,
			'house_no' => '',
		);
		$matches = array();
		if(preg_match('_(.*?)(\d.*)_', $street_address, $matches) === 1)
		{
			$splitStreet['street']   = $matches[1];
			$splitStreet['house_no'] = $matches[2];
		}
		return $splitStreet;
	}

	/**
	 * set order status and (optionally) notify customer by email
	 * @param int orders_id
	 * @param int orders_status_id
	 * @param string $order_status_comment
	 * @param boolean $notifyCustomer
	 */
    protected function setOrderStatus($orders_id, $order_status_id, $order_status_comment = '', $notifyCustomer = false)
    {
        /** @var OrderWriteService $orderWriteService */
        $orderWriteService = StaticGXCoreLoader::getService('OrderWrite');
        
        if ($order_status_id > 0) {
            $this->internetMarkeLogger->notice(sprintf('changing orders status of order %s to %s',
                                                       $orders_id,
                                                       $order_status_id));
            $orderWriteService->updateOrderStatus(new IdType((int)$orders_id),
                                                  new IntType((int)$order_status_id),
                                                  new StringType($order_status_comment),
                                                  new BoolType($notifyCustomer));
            if ($notifyCustomer === true) {
                $this->internetMarkeLogger->notice(sprintf('sending email notification regarding status change of order %s',
                                                           $orders_id));
                $this->notifyCustomer($orders_id, $order_status_id, $order_status_comment);
            }
        } else {
            $orderWriteService->addOrderStatusHistoryEntry(new IdType((int)$orders_id),
                                                           new StringType($order_status_comment),
                                                           new IdType((int)$_SESSION['customer_id']));
        }
    }
    
    
    /**
	 * notify customer of a change in order status
	 *
	 * This is mostly copypasted from orders.php and MUST be refactored ASAP!
	 */
	protected function notifyCustomer($orders_id, $orders_status_id, $order_status_comment)
	{
		require_once DIR_FS_INC . 'xtc_php_mail.inc.php';
		require_once DIR_WS_CLASSES.'order.php';
		$order       = new order((int)$orders_id);
		$lang_query  = sprintf('select languages_id from %s where directory = \'%s\'', TABLE_LANGUAGES, $order->info['language']);
		$lang_result = xtc_db_query($lang_query);
		while($lang_row = xtc_db_fetch_array($lang_result))
		{
			$lang = empty($lang_row['languages_id']) ? $_SESSION['languages_id'] : $lang_row['languages_id'];
		}
		$orders_status_array  = array ();
		$orders_status_query  = sprintf('select orders_status_id, orders_status_name from %s where language_id = \'%s\'', TABLE_ORDERS_STATUS, $lang);
		$orders_status_result = xtc_db_query($orders_status_query);
		while($orders_status_row = xtc_db_fetch_array($orders_status_result))
		{
			$orders_status_array[$orders_status_row['orders_status_id']] = $orders_status_row['orders_status_name'];
		}

		$smarty = MainFactory::create('GXSmarty');
		// assign language to template for caching
		$smarty->assign('language', $_SESSION['language']);
		$smarty->caching      = false;
        $smarty->template_dir = DIR_FS_CATALOG . StaticGXCoreLoader::getThemeControl()->getThemeHtmlPath();
		$smarty->config_dir   = DIR_FS_CATALOG.'lang';
        $smarty->assign('tpl_path', DIR_FS_CATALOG . StaticGXCoreLoader::getThemeControl()->getThemeHtmlPath());
        $smarty->assign('logo_path',
                        HTTP_SERVER . DIR_WS_CATALOG . StaticGXCoreLoader::getThemeControl()->getThemeImagePath());
		$smarty->assign('NAME', $order->customer['name']);
		$smarty->assign('GENDER', $order->customer['gender']);
		$smarty->assign('ORDER_NR', $orders_id);
		$smarty->assign('ORDER_LINK', xtc_catalog_href_link(FILENAME_CATALOG_ACCOUNT_HISTORY_INFO, 'order_id='.$orders_id, 'SSL'));
		$smarty->assign('ORDER_DATE', xtc_date_long($order->info['date_purchased']));
		$smarty->assign('ORDER_STATUS', $orders_status_array[$orders_status_id]);
		if(defined('EMAIL_SIGNATURE'))
		{
			$smarty->assign('EMAIL_SIGNATURE_HTML', nl2br(EMAIL_SIGNATURE));
			$smarty->assign('EMAIL_SIGNATURE_TEXT', EMAIL_SIGNATURE);
		}
        if(defined('EMAIL_HTML_SIGNATURE'))
        {
            $smarty->assign('EMAIL_SIGNATURE_HTML', EMAIL_HTML_SIGNATURE);
            $smarty->assign('EMAIL_SIGNATURE_TEXT', EMAIL_SIGNATURE);
        }

		// START Parcel Tracking Code
		/** @var ParcelTrackingCode $coo_parcel_tracking_code_item */
        $coo_parcel_tracking_code_item = MainFactory::create('ParcelTrackingCode');
		/** @var ParcelTrackingCodeReader $coo_parcel_tracking_code_reader */
        $coo_parcel_tracking_code_reader = MainFactory::create('ParcelTrackingCodeReader');
		$t_parcel_tracking_codes_array   = $coo_parcel_tracking_code_reader->getTackingCodeItemsByOrderId($coo_parcel_tracking_code_item,
																								  $orders_id);
		$smarty->assign('PARCEL_TRACKING_CODES_ARRAY', $t_parcel_tracking_codes_array);
		$smarty->assign('PARCEL_TRACKING_CODES', 'true');
		// END Parcel Tracking Code

		$smarty->assign('NOTIFY_COMMENTS', nl2br($order_status_comment));
		$html_mail = fetch_email_template($smarty, 'change_order_mail', 'html');
		$smarty->assign('NOTIFY_COMMENTS', $order_status_comment);
		$txt_mail  = fetch_email_template($smarty, 'change_order_mail', 'txt');

		if($_SESSION['language'] == 'german')
		{
			$subject = 'Ihre Bestellung '.$orders_id.', '.xtc_date_long($order->info['date_purchased']).', '.$order->customer['name'];
		}
		else
		{
			$subject = 'Your order '.$orders_id.', '.xtc_date_long($order->info['date_purchased']).', '.$order->customer['name'];
		}

		xtc_php_mail(
			EMAIL_BILLING_ADDRESS,
			EMAIL_BILLING_NAME,
			$order->customer['email_address'],
			$order->customer['name'],
			'',
			EMAIL_BILLING_REPLY_ADDRESS,
			EMAIL_BILLING_REPLY_ADDRESS_NAME,
			'',
			'',
			$subject,
			$html_mail,
			$txt_mail
		);
	}


	/* =================================================================================== */
	/* =================================================================================== */

	public function actionWallet()
	{
		$walletUrl = 'https://portokasse.deutschepost.de/portokasse/';
		return MainFactory::create('RedirectHttpControllerResponse', $walletUrl);
	}

}
