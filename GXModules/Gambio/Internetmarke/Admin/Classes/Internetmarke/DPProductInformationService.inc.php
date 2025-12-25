<?php
/* --------------------------------------------------------------
	DPProductInformationService.inc.php 2019-08-01
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

class DPProductInformationService
{
	protected $configuration;
	protected $soapClient;
	protected $internetMarkeLogger;

	const MAX_CACHEFILE_AGE = 86400;

	public function __construct()
	{
		$this->configuration       = MainFactory::create('InternetMarkeConfigurationStorage');
		$this->internetMarkeLogger = MainFactory::create('InternetMarkeLogger');
	}

	protected function formatXmlString($input)
	{
		$dom                     = new DOMDocument();
		$dom->recover            = true;
		$dom->loadXML($input);
		$dom->formatOutput       = true;
		$dom->preserveWhiteSpace = false;
		$output                  = $dom->saveXML();
		return $output;
	}

	protected function _log($message)
	{
		$this->internetMarkeLogger->notice(sprintf("%s: %s", __CLASS__, $message));
	}

	protected function logSoapFault(SoapFault $sf)
	{
		$soap_responseheaders = $this->soapClient->__getLastResponseHeaders();
		$soap_response        = $this->soapClient->__getLastResponse();
		$soap_requestheaders  = $this->soapClient->__getLastRequestHeaders();
		$soap_request         = $this->soapClient->__getLastRequest();
		$log = "SoapFault in getSoapClient\n";
		$log .= "ResponseHeaders:\n\n";
		$log .= $soap_responseheaders;
		$log .= "\n\nResponse:\n\n";
		$log .= $this->formatXmlString($soap_response);
		$log .= "\n\nRequestHeaders:\n\n";
		$log .= $soap_requestheaders;
		$log .= "\n\nRequest:\n\n";
		$log .= $this->formatXmlString($soap_request);
		$log .= "\n\nSoapFault:\n\n";
		$log .= print_r($sf, true);
		$this->_log($log);
	}

	protected function convertForXML($string)
	{
		$converted = htmlentities($string, ENT_QUOTES|ENT_XML1);
		return $converted;
	}

	public function getSoapClient() {
		if(!($this->soapClient instanceof SoapClient)) {
			try {
				$soap_params = array(
					'trace'      => true,
					'exceptions' => true,
					'cache_wsdl' => WSDL_CACHE_NONE,
					'user_agent' => 'Gambio GX2',
					'features'   => SOAP_SINGLE_ELEMENT_ARRAYS,
				);
				$this->soapClient = new SoapClient($this->configuration->get('prodws/endpoint_live'), $soap_params);
				$headers          = array();
				$ns_wsse          = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd';

				$security_header = '<wsse:Security xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd" xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd">'.
					'<wsse:UsernameToken wsu:Id="UsernameToken-47695D4677F169938C14458490956322">'.
					'<wsse:Username>USERNAME</wsse:Username>'.
					'<wsse:Password Type="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText">PASSWORD</wsse:Password>'.
					'</wsse:UsernameToken></wsse:Security>';
				$security_header = strtr($security_header, array(
					'USERNAME' => $this->convertForXML($this->configuration->get('prodws/credentials/user')),
					'PASSWORD' => $this->convertForXML($this->configuration->get('prodws/credentials/password')),
				));
				$header_security = new SoapVar($security_header, XSD_ANYXML);
				$headers[]       = new SoapHeader($ns_wsse, 'Security', $header_security);
				$this->soapClient->__setSoapHeaders($headers);
			}
			catch(SoapFault $sf) {
				$this->logSoapFault($sf);
				return false;
			}
		}
		return $this->soapClient;
	}


	public function getProductList($forceUpdate = false)
	{
		$cacheFile = DIR_FS_CATALOG.'cache/dp_prodws-'.LogControl::get_secure_token().'.pdc';

		if(file_exists($cacheFile))
		{
			$cacheFileAge = time() - filemtime($cacheFile);
			if($cacheFileAge > self::MAX_CACHEFILE_AGE)
			{
				$this->_log('cache too old, forcing update');
				$forceUpdate = true;
			}
		}

		if($forceUpdate === false && file_exists($cacheFile))
		{
			$productList = unserialize(file_get_contents($cacheFile));
			$this->_log(sprintf("using cached ProductList (%s)", (string)$productList->Response->date));
		}
		else
		{
			$soapClient = $this->getSoapClient();
			try
			{
				$productListRequestParameters = array(
					'mandantID'         => $this->configuration->get('prodws/credentials/id'),
					'dedicatedProducts' => false,
					'responseMode'      => 0,
				);
				$productList = $soapClient->getProductList($productListRequestParameters);
				// file_put_contents(DIR_FS_CATALOG.'/logfiles/productlist-response.txt', $this->soapClient->__getLastResponse());
				if($productList->success == true)
				{
					file_put_contents($cacheFile, serialize($productList));
					$this->_log(sprintf("received ProductList dated %s", (string)$productList->Response->date));
				}
				else
				{
					throw new Exception((string)$productList->Exception);
				}
			}
			catch(SoapFault $sf)
			{
				$this->logSoapFault($sf);
			}
		}
		return $productList;
	}

	public function getProductListDate()
	{
		$productList = $this->getProductList();
		$date = (string)$productList->Response->date;
		return $date;
	}

	public function getPPLProductList($forceUpdate = false)
	{
		$rawProductList = $this->getProductList($forceUpdate);
		$pplProducts = array();
		foreach($rawProductList->Response->salesProductList->SalesProduct as $product)
		{
			if($product->extendedIdentifier->externIdentifier[0]->source == 'PPL')
			{
				$pplProducts[(int)$product->extendedIdentifier->externIdentifier[0]->id] = $product;
			}
		}
		ksort($pplProducts);
		return $pplProducts;
	}

	public function getFilteredPPLProductList(array $contractProducts, $forcesUpdate = false)
    {
        $pplProducts = $this->getPPLProductList($forcesUpdate);
        $filteredPplProducts = array_intersect_key($pplProducts, $contractProducts);
	    
	    return $filteredPplProducts;
    }
	
	public function getProductCost($productCode)
	{
		$productCost = false;
		$products = $this->getProductList();
		foreach($products->Response->salesProductList->SalesProduct as $product)
		{
			if($product->extendedIdentifier->externIdentifier[0]->id == $productCode)
			{
				$productCost = $product->priceDefinition->price->calculatedGrossPrice->value;
			}
		}
		if($productCost === false)
		{
			throw new Exception('Product not found');
		}
		return $productCost;
	}
}
