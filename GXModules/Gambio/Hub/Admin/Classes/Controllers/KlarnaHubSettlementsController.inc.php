<?php
/* --------------------------------------------------------------
   KlarnaHubSettlementsController.inc.php 2023-04-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

use HubPublic\Exceptions\CurlRequestException;
use \HubPublic\Http\CurlRequest;

class KlarnaHubSettlementsController extends AdminHttpViewController
{
	protected $languageTextManager;
	protected $templatesBaseDir;
	protected $hubAssetHelper;
	
	
	const PAGE_SIZE_DEFAULT = 20;
	
	
	public function init()
	{
		$this->languageTextManager = MainFactory::create('LanguageTextManager', 'klarna_settlements',
		                                                 $_SESSION['languages_id']);
		$installedVersion          = gm_get_conf('INSTALLED_VERSION');
		$this->hubAssetHelper      = MainFactory::create('HubAssetHelper', $installedVersion);
		
		$this->templatesBaseDir = DIR_FS_CATALOG . $this->hubAssetHelper->getTemplatesBasePath();
	}
	
	
	public function actionDefault()
	{
		$hubRegistered = gm_get_conf('GAMBIO_HUB_CLIENT_KEY') !== null
		                 && gm_get_conf('GAMBIO_HUB_CLIENT_KEY') !== '';
		if(!$hubRegistered)
		{
			return MainFactory::create('RedirectHttpControllerResponse',
			                           DIR_WS_ADMIN . 'admin.php?do=HubConfiguration/account');
		}
		
		$klarnaHubInstalled = gm_get_conf('GAMBIO_HUB_REMOTE_CONFIG_KLARNAHUB_DEBUGLOGGING') !== null
		                      && gm_get_conf('GAMBIO_HUB_REMOTE_CONFIG_KLARNAHUB_DEBUGLOGGING') !== '';
		
		if(!$klarnaHubInstalled)
		{
			return MainFactory::create('RedirectHttpControllerResponse',
			                           DIR_WS_ADMIN . 'admin.php?do=HubConfiguration/paymentMethods');
		}
		
		$startDate    = $this->_getQueryParameter('start_date');
		$endDate      = $this->_getQueryParameter('end_date');
		$offset       = $this->_getQueryParameter('offset');
		$skipToOffset = $this->_getQueryParameter('skip_to_offset');
		$offset       = $offset === null ? $skipToOffset : $offset;
		$pageSize     = self::PAGE_SIZE_DEFAULT;
		if(empty($startDate) || empty($endDate) || !is_numeric($offset))
		{
			$redirectParams = http_build_query([
				                                   'do'         => 'KlarnaHubSettlements',
				                                   'start_date' => (new DateTime('30 days ago'))->format('Y-m-d'),
				                                   'end_date'   => (new DateTime('now'))->format('Y-m-d'),
				                                   'offset'     => '0',
			                                   ], '', '&');
			$redirectUrl    = xtc_href_link('admin.php', $redirectParams);
			
			return MainFactory::create('RedirectHttpControllerResponse', $redirectUrl);
		}
		$offset        = (int)$offset;
		$startDatetime = new DateTime($startDate);
		$endDatetime   = new DateTime($endDate);
		
		try
		{
			$hubResponse = $this->retrieveDataFromHub([
				                                          'action'     => 'payoutsSummary',
				                                          'start_date' => $startDatetime->format('Y-m-d'),
				                                          'end_date'   => $endDatetime->format('Y-m-d'),
				                                          'offset'     => $offset,
			                                          ]);
		}
		catch(CurlRequestException $e)
		{
			$text        = MainFactory::create('LanguageTextManager', 'klarna_settlements');
			$hubResponse = [
				'html'     => $text->get_text('data_not_received'),
				'messages' => [
					[
						'text' => $text->get_text('error_retrieving_data_from_hub'),
						'type' => 'error',
					]
				]
			];
		}
		
		$output = 'no output';
		if(!empty($hubResponse['html']))
		{
			$output = $hubResponse['html'];
			$output = str_replace([
				                      '%shopurl_payoutssummaryreport%',
				                      '%shopurl_payouttransactionsreport%',
				                      '%shopurl_payout%',
			                      ], [
				                      xtc_href_link('admin.php', 'do=KlarnaHubSettlements/PayoutsSummaryReport'),
				                      xtc_href_link('admin.php',
				                                    'do=KlarnaHubSettlements/PayoutWithTransactionsReport'),
				                      xtc_href_link('admin.php', 'do=KlarnaHubSettlements/Payout')
			                      ], $output);
			$output .= $this->getStyles();
		}
		if(!empty($hubResponse['messages']))
		{
			foreach($hubResponse['messages'] as $message)
			{
				$GLOBALS['messageStack']->add($message['text'], $message['type']);
			}
		}
		
		$title    = new NonEmptyStringType($this->languageTextManager->get_text('heading'));
		$template = new ExistingFile(new NonEmptyStringType($this->templatesBaseDir . '/klarna_settlements.html'));
		
		$pages          = [];
		$prevPageOffset = 0;
		$nextPageOffset = 0;
		$maxPage        = 1;
		$lastPage       = 0;
		if(!empty($hubResponse['pagination']))
		{
			for($pageOffset = 0, $page = 1; $pageOffset
			                                < $hubResponse['pagination']['total']; $pageOffset += $pageSize, $page++)
			{
				$pages[$page] = ['offset' => $pageOffset, 'page' => $page];
			}
			$prevPageOffset = ($hubResponse['pagination']['offset'] - $pageSize)
			                  >= 0 ? $hubResponse['pagination']['offset'] - $pageSize : false;
			$nextPageOffset = ($hubResponse['pagination']['offset'] + $pageSize)
			                  <= $hubResponse['pagination']['total'] ? $hubResponse['pagination']['offset']
			                                                           + $pageSize : false;
			$maxPage        = ceil($hubResponse['pagination']['total'] / $pageSize);
			$lastPage       = empty($pages) ? $hubResponse['pagination']['offset'] : $pages[max(array_keys($pages))]['offset'];
		}
		
		$pageData    = [
			'start_date' => $startDatetime->format('Y-m-d'),
			'end_date'   => $endDatetime->format('Y-m-d'),
			'offset'     => $hubResponse['pagination']['offset'] ?? 0,
			'count'      => $hubResponse['pagination']['count'] ?? 0,
			'total'      => $hubResponse['pagination']['total'] ?? 0,
			'pages'      => $pages,
			'page_size'  => $pageSize,
			'prevpage'   => $prevPageOffset,
			'nextpage'   => $nextPageOffset,
			'maxpage'    => $maxPage,
			'lastpage'   => $lastPage,
		];
		$data        = MainFactory::create('KeyValueCollection', $pageData);
		$assetsArray = [
			new Asset(DIR_WS_CATALOG . $this->hubAssetHelper->getStylesBaseUrl() . '/KlarnaSettlements.css'),
		];
		$assets      = MainFactory::create('AssetCollection', $assetsArray);
		//$output .= sprintf("<pre>%s</pre>", print_r($pages, true));
		
		ob_start();
		$response    = MainFactory::create('AdminLayoutHttpControllerResponse', $title, $template, $data, $assets);
		$pageContent = ob_get_clean();
		$pageContent = str_replace('%hub_output%', $output, $pageContent);
		echo $pageContent;
		
		return $response;
	}
	
	
	/**
	 *
	 */
	public function actionPayout()
	{
		$paymentReference = $this->_getQueryParameter('payment_reference');
		if(preg_match('/^[a-f0-9-]+$/', $paymentReference) !== 1)
		{
			throw new RuntimeException('invalid payment reference');
		}
		
		$offset       = $this->_getQueryParameter('offset');
		$skipToOffset = $this->_getQueryParameter('skip_to_offset');
		$offset       = $offset === null ? $skipToOffset : $offset;
		$pageSize     = self::PAGE_SIZE_DEFAULT;
		
		$hubResponse = $this->retrieveDataFromHub([
			                                          'action'            => 'payout',
			                                          'payment_reference' => $paymentReference,
			                                          'offset'            => $offset,
			                                          'size'              => $pageSize,
		                                          ]);
		
		$output = 'no output';
		if(!empty($hubResponse['html']))
		{
			$output = $hubResponse['html'];
			$output = str_replace([
				                      '%shopurl_payouttransactionsreport%',
			                      ], [
				                      xtc_href_link('admin.php',
				                                    'do=KlarnaHubSettlements/PayoutWithTransactionsReport'),
			                      ], $output);
			$output .= $this->getStyles();
		}
		$title    = new NonEmptyStringType($this->languageTextManager->get_text('heading'));
		$template = new ExistingFile(new NonEmptyStringType($this->templatesBaseDir . '/klarna_payout.html'));
		$pages    = [];
		for($pageOffset = 0, $page = 1; $pageOffset
		                                < $hubResponse['pagination']['total']; $pageOffset += $pageSize, $page++)
		{
			$pages[$page] = ['offset' => $pageOffset, 'page' => $page];
		}
		$prevPageOffset = ($hubResponse['pagination']['offset'] - $pageSize) >= 0 ? $hubResponse['pagination']['offset']
		                                                                            - $pageSize : false;
		$nextPageOffset = ($hubResponse['pagination']['offset'] + $pageSize)
		                  <= $hubResponse['pagination']['total'] ? $hubResponse['pagination']['offset']
		                                                           + $pageSize : false;
		
		$pageData    = [
			'payment_reference' => $paymentReference,
			'pages'             => $pages,
			'prevpage'          => $prevPageOffset,
			'nextpage'          => $nextPageOffset,
			'maxpage'           => ceil($hubResponse['pagination']['total'] / $pageSize),
			'lastpage'          => empty($pages) ? $hubResponse['pagination']['offset'] : $pages[max(array_keys($pages))]['offset'],
		];
		$data        = MainFactory::create('KeyValueCollection', $pageData);
		$assetsArray = [];
		$assets      = MainFactory::create('AssetCollection', $assetsArray);
		ob_start();
		$response    = MainFactory::create('AdminLayoutHttpControllerResponse', $title, $template, $data, $assets);
		$pageContent = ob_get_clean();
		$pageContent = str_replace('%hub_output%', $output, $pageContent);
		echo $pageContent;
		
		return $response;
	}
	
	protected function getStyles()
	{
		$styles = [
			'div.dlbuttons a.export-pdf { display: inline !important; }'
		];
		$stylesBlock = '<style>' . implode("\n", $styles) . '</style>';
		return $stylesBlock;
	}
	
	/**
	 * Offers a Payout Summary Report (CSV) for download
	 *
	 * @return bool|\HttpControllerResponse
	 */
	public function actionPayoutsSummaryReport()
	{
		$startDate     = $this->_getQueryParameter('start_date');
		$endDate       = $this->_getQueryParameter('end_date');
		$startDatetime = new DateTime($startDate);
		$endDatetime   = new DateTime($endDate);
		$format        = $this->_getQueryParameter('format');
		$format        = in_array($format, ['csv', 'pdf']) ? $format : 'csv';
		
		$hubResponse = $this->retrieveDataFromHub([
			                                          'action'     => 'payoutsSummaryReport',
			                                          'start_date' => $startDatetime->format('Y-m-d'),
			                                          'end_date'   => $endDatetime->format('Y-m-d'),
			                                          'format'     => $format,
		                                          ]);
		if(!empty($hubResponse['csv']))
		{
			$filename = 'klarna-payouts-summary-' . $startDatetime->format('Y-m-d') . '-'
			            . $endDatetime->format('Y-m-d') . '.csv';
			$headers  = [
				'Content-Type: text/csv',
				'Content-Disposition: attachment; filename="' . $filename . '"',
			];
			$response = MainFactory::create('HttpControllerResponse', $hubResponse['csv'], $headers);
		}
		else if(!empty($hubResponse['pdf']))
		{
			$filename = 'klarna-payouts-summary-' . $startDatetime->format('Y-m-d') . '-'
			            . $endDatetime->format('Y-m-d') . '.pdf';
			$headers  = [
				'Content-Type: application/pdf',
				'Content-Disposition: attachment; filename="' . $filename . '"',
			];
			$response = MainFactory::create('HttpControllerResponse', base64_decode($hubResponse['pdf']), $headers);
		}
		else
		{
			$message  = 'Error generating report';
			$headers  = ['Content-Type: text/plain'];
			$response = MainFactory::create('HttpControllerResponse', $message, $headers);
		}
		
		return $response;
	}
	
	
	/**
	 * @throws \Exception
	 */
	public function actionPayoutWithTransactionsReport()
	{
		$paymentReference = $this->_getQueryParameter('payment_reference');
		if(preg_match('/^[a-f0-9-]+$/', $paymentReference) !== 1)
		{
			throw new Exception('invalid payment reference');
		}
		$format        = $this->_getQueryParameter('format');
		$format        = in_array($format, ['csv', 'pdf']) ? $format : 'csv';
		$hubResponse = $this->retrieveDataFromHub([
			                                          'action'            => 'payoutWithTransactionsReport',
			                                          'payment_reference' => $paymentReference,
			                                          'format'            => $format,
		                                          ]);
		if(!empty($hubResponse['csv']))
		{
			$filename = 'klarna-payout-with-transactions-' . $paymentReference . '.csv';
			$headers  = [
				'Content-Type: text/csv',
				'Content-Disposition: attachment; filename="' . $filename . '"',
			];
			$response = MainFactory::create('HttpControllerResponse', $hubResponse['csv'], $headers);
		}
		else if(!empty($hubResponse['pdf']))
		{
			$filename = 'klarna-payout-with-transactions-' . $paymentReference . '.pdf';
			$headers  = [
				'Content-Type: application/pdf',
				'Content-Disposition: attachment; filename="' . $filename . '"',
			];
			$response = MainFactory::create('HttpControllerResponse', base64_decode($hubResponse['pdf']), $headers);
		}
		else
		{
			$message  = 'Error generating report';
			$headers  = ['Content-Type: text/plain'];
			$response = MainFactory::create('HttpControllerResponse', $message, $headers);
		}
		
		return $response;
	}
	
	
	/**
	 * @param $input
	 *
	 * @return array|mixed
	 */
	protected function retrieveDataFromHub($parameters)
	{
		$query = [
			'client_key' => gm_get_conf('GAMBIO_HUB_CLIENT_KEY'),
			'language'   => strtolower($_SESSION['language_code']),
			'devmode'    => file_exists(DIR_FS_CATALOG . '.dev-environment') ? 'true' : 'false',
		];
		$query = array_merge($parameters, $query);
		
		/** @var HubSettings $hubSettings */
		$hubSettings = MainFactory::create('HubSettings', gm_get_conf('GAMBIO_HUB_CURL_TIMEOUT'));
		
		/** @var \HubCallbackApiClient $hubCallbackApiClient */
		$hubCallbackApiClient = MainFactory::create('HubCallbackApiClient', MODULE_PAYMENT_GAMBIO_HUB_URL,
		                                            new CurlRequest(), LogControl::get_instance(), $hubSettings);
		
		try
		{
			/** @var \HttpResponse $response */
			$response = $hubCallbackApiClient->execute('KlarnaHub', true, ['source' => 'settlements'], $query);
			
			if($response->getStatusCode() !== 200)
			{
				throw new RuntimeException('Error sending configuration to Hub');
			}
			
			$responseBody = json_decode($response->getBody(), true);
		}
		catch(Exception $exception)
		{
			if(strpos($exception->getMessage(), '?source=settlements') === false)
			{ // Offending URL not found. 
				throw $exception; // Re-throw the exception so that it bubbles up. 
			}
			
			// Suppress the settlements callback error as older Hub releases won't support it anyway.
			$responseBody = [];
		}
		
		return $responseBody;
	}
}
