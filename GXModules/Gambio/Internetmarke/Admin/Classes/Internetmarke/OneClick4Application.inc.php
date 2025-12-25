<?php
/* --------------------------------------------------------------
	OneClick4Application.inc.php 2023-04-28
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

class OneClick4Application
{
    const PARTNER_ID = 'AGOGA';
    const MAX_CACHEFILE_AGE = 86400;
    protected $configuration;
    protected $soapClient;
    protected $logger;
    
    
    public function __construct()
    {
        $this->configuration = MainFactory::create('InternetMarkeConfigurationStorage');
        $this->logger        = MainFactory::create('InternetMarkeLogger');
    }
    
    
    public function retrievePageFormatsList()
    {
        $formatsResponse = $this->retrievePageFormats();
        $formatsList     = [];
        foreach ($formatsResponse->pageFormat as $pageFormat) {
            $formatsList[$pageFormat->id] = $pageFormat;
        }
        ksort($formatsList);
        
        return $formatsList;
    }
    
    
    public function retrievePageFormats()
    {
        $response = false;
        $sc       = $this->getSoapClient();
        try {
            $response = $sc->retrievePageFormats();
        } catch (SoapFault $sf) {
            $this->logSoapFault($sf);
        }
        
        return $response;
    }
    
    
    public function getSoapClient()
    {
        if (!($this->soapClient instanceof SoapClient)) {
            try {
                $soap_params             = [
                    'trace'      => true,
                    'exceptions' => true,
                    'cache_wsdl' => WSDL_CACHE_NONE,
                    'user_agent' => 'Gambio GX2',
                    'features'   => SOAP_SINGLE_ELEMENT_ARRAYS,
                ];
                $request_timestamp       = date('dmY-His');
                $partner_signature_parts = [
                    trim(self::PARTNER_ID),
                    trim($request_timestamp),
                    trim($this->configuration->get('oneclick4app/key_phase')),
                    trim($this->configuration->get('oneclick4app/key')),
                ];
                $partner_signature       = substr(md5(implode('::', $partner_signature_parts)), 0, 8);
                // $this->_log('endpoint: '.$this->configuration->get('oneclick4app/endpoint_live'));
                $this->soapClient = new SoapClient(
                    $this->configuration->get('oneclick4app/endpoint_live'), $soap_params
                );
                $headers          = [];
                $ns_onec          = 'http://oneclickforpartner.dpag.de';
                $headers[]        = new SoapHeader($ns_onec, 'PARTNER_ID', new SoapVar(self::PARTNER_ID, XSD_STRING));
                $headers[]        = new SoapHeader(
                    $ns_onec,
                    'REQUEST_TIMESTAMP',
                    new SoapVar($request_timestamp, XSD_STRING)
                );
                $headers[]        = new SoapHeader(
                    $ns_onec,
                    'KEY_PHASE',
                    new SoapVar(
                        $this->configuration->get('oneclick4app/key_phase'),
                        XSD_STRING
                    )
                );
                $headers[]        = new SoapHeader(
                    $ns_onec,
                    'PARTNER_SIGNATURE',
                    new SoapVar($partner_signature, XSD_STRING)
                );
                $this->soapClient->__setSoapHeaders($headers);
            } catch (SoapFault $sf) {
                $this->logSoapFault($sf);
                
                return false;
            }
        }
        
        return $this->soapClient;
    }
    
    
    protected function logSoapFault(SoapFault $sf)
    {
        $soap_responseheaders = $this->soapClient->__getLastResponseHeaders();
        $soap_response        = $this->soapClient->__getLastResponse();
        $soap_requestheaders  = $this->soapClient->__getLastRequestHeaders();
        $soap_request         = $this->soapClient->__getLastRequest();
        $log                  = "SoapFault in getSoapClient\n";
        $log                  .= "ResponseHeaders:\n\n";
        $log                  .= $soap_responseheaders;
        $log                  .= "\n\nResponse:\n\n";
        $log                  .= $this->formatXmlString($soap_response);
        $log                  .= "\n\nRequestHeaders:\n\n";
        $log                  .= $soap_requestheaders;
        $log                  .= "\n\nRequest:\n\n";
        $log                  .= $this->formatXmlString($soap_request);
        $log                  .= "\n\nSoapFault:\n\n";
        //$log .= print_r($sf, true);
        $log .= (string)$sf . "\n\n";
        $this->_log($log);
        throw $sf;
    }
    
    
    protected function formatXmlString($input)
    {
        $dom          = new DOMDocument();
        $dom->recover = true;
        $dom->loadXML($input);
        $dom->formatOutput       = true;
        $dom->preserveWhiteSpace = false;
        $output                  = $dom->saveXML();
        
        return $output;
    }
    
    
    protected function _log($message)
    {
        $this->logger->notice($message);
    }
    
    
    public function retrievePublicGalleryCategories()
    {
        $response   = $this->retrievePublicGallery();
        $categories = [];
        foreach ($response->items as $galleryItem) {
            if (!array_key_exists($galleryItem->categoryId, $categories)) {
                $categories[$galleryItem->categoryId] = [
                    'name'   => (string)$galleryItem->categoryDescription,
                    'images' => [],
                ];
            }
            $categories[$galleryItem->categoryId]['images'][] = $galleryItem->images[0];
            // fix for corrupted thumbnail URLs which first appeared on or around 2016-08-12
            foreach ($categories[$galleryItem->categoryId]['images'] as $imageIndex => $imageData) {
                $imageData->links->linkThumbnail = str_replace(
                    'https://https://',
                    'https://',
                    (string)$imageData->links->linkThumbnail
                );
            }
            // end fix
        }
        
        return $categories;
    }
    
    
    public function retrievePublicGallery()
    {
        $response = false;
        $sc       = $this->getSoapClient();
        try {
            $response = $sc->retrievePublicGallery();
        } catch (SoapFault $sf) {
            $this->logSoapFault($sf);
        }
        
        return $response;
    }
    
    
    public function retrievePrivateGallery()
    {
        $response = false;
        $sc       = $this->getSoapClient();
        try {
            $userTokenResponse             = $this->authenticateUser();
            $userToken                     = $userTokenResponse->userToken;
            $retrievePrivateGalleryRequest = [
                'userToken' => $userToken,
            ];
            $response                      = $sc->retrievePrivateGallery($retrievePrivateGalleryRequest);
        } catch (SoapFault $sf) {
            $this->logSoapFault($sf);
        }
        
        return $response;
    }
    
    
    public function authenticateUser()
    {
        $response            = false;
        $sc                  = $this->getSoapClient();
        $authenticateRequest = [
            'username' => $this->configuration->get('oneclick4app/credentials/email'),
            'password' => $this->configuration->get('oneclick4app/credentials/password'),
        ];
        try {
            $response = $sc->authenticateUser($authenticateRequest);
        } catch (SoapFault $sf) {
            $this->logSoapFault($sf);
        }
        
        return $response;
    }
    
    
    public function retrievePreviewVoucherPDF($productCode, $imageID, $voucherLayout, $pageFormatID)
    {
        $response = false;
        $sc       = $this->getSoapClient();
        try {
            $retrievePreviewVoucherPDFRequest = [
                'productCode'   => $productCode,
                'imageID'       => $imageID,
                'voucherLayout' => $voucherLayout,
                'pageFormatId'  => $pageFormatID,
            ];
            $response                         = $sc->retrievePreviewVoucherPDF($retrievePreviewVoucherPDFRequest);
        } catch (SoapFault $sf) {
            $this->logSoapFault($sf);
        }
        
        return $response;
    }
    
    
    public function retrievePreviewVoucherPNG($productCode, $imageID, $voucherLayout)
    {
        $response = false;
        $sc       = $this->getSoapClient();
        try {
            $retrievePreviewVoucherPNGRequest = [
                'productCode'   => $productCode,
                //'imageID'     => $imageID,
                'voucherLayout' => $voucherLayout,
            ];
            if (!empty($imageID) && (int)$imageID > 0) {
                $retrievePreviewVoucherPNGRequest['imageID'] = (string)$imageID;
            }
            $response = $sc->retrievePreviewVoucherPNG($retrievePreviewVoucherPNGRequest);
        } catch (SoapFault $sf) {
            $this->logSoapFault($sf);
        }
        
        return $response;
    }
    
    
    public function checkoutShoppingCart($positions, $pageFormatID, $total, $output = 'pdf')
    {
        $checkoutShoppingCartRequest = [
            'positions' => $positions,
            'total'     => (int)$total,
        ];
        $response                    = false;
        $sc                          = $this->getSoapClient();
        try {
            $shopOrderIdResponse                        = $this->createShopOrderId();
            $checkoutShoppingCartRequest['shopOrderId'] = $shopOrderIdResponse->shopOrderId;
            
            $userTokenResponse                        = $this->authenticateUser();
            $userToken                                = $userTokenResponse->userToken;
            $checkoutShoppingCartRequest['userToken'] = $userToken;
            
            if ($output == 'png') {
                $response = $sc->checkoutShoppingCartPNG($checkoutShoppingCartRequest);
            } else {
                $checkoutShoppingCartRequest['pageFormatId'] = $pageFormatID;
                $response                                    = $sc->checkoutShoppingCartPDF(
                    $checkoutShoppingCartRequest
                );
            }
            $this->logger->notice(
                sprintf(
                    "checkoutShoppingCart, request:\n%s\nresponse:%s\n",
                    print_r($checkoutShoppingCartRequest, true),
                    print_r($response, true)
                )
            );
        } catch (SoapFault $sf) {
            $this->logSoapFault($sf);
            throw $sf;
        }
        
        return $response;
    }
    
    public function createShopOrderId()
    {
        $response = false;
        $sc       = $this->getSoapClient();
        try {
            $userTokenResponse        = $this->authenticateUser();
            $userToken                = $userTokenResponse->userToken;
            $createShopOrderIdRequest = [
                'userToken' => $userToken,
            ];
            $response                 = $sc->createShopOrderId($createShopOrderIdRequest);
        } catch (SoapFault $sf) {
            $this->logSoapFault($sf);
        }
        
        return $response;
    }
    
    public function retrieveContractProducts($forceUpdate = false)
    {
        $contractProducts = [];

        $cacheFile = DIR_FS_CATALOG.'cache/dp_contractproducts-'.LogControl::get_secure_token().'.pdc';
        if (!$forceUpdate && file_exists($cacheFile) && (time() - filemtime($cacheFile)) < static::MAX_CACHEFILE_AGE) {
            $contractProducts = json_decode(file_get_contents($cacheFile), true);
            $this->_log('using cached contract products');
            return $contractProducts;
        }

        $this->_log('retrieving contract products');
        $sc = $this->getSoapClient();
        try {
            $userTokenResponse             = $this->authenticateUser();
            $userToken                     = $userTokenResponse->userToken;
            $retrieveContractProductsRequest = [
                'userToken' => $userToken,
            ];
            $contractProductsResponse = $sc->retrieveContractProducts($retrieveContractProductsRequest);
            
            if (isset($contractProductsResponse->products) && is_iterable($contractProductsResponse->products)) {
                foreach ($contractProductsResponse->products as $product) {
                    $contractProducts[(int)$product->productCode] = [];
                    if (isset($product->price)) {
                        $contractProducts[(int)$product->productCode]['price'] = $product->price;
                    }
                }
            }
            
            file_put_contents($cacheFile, json_encode($contractProducts));
        } catch (SoapFault $sf) {
            $this->logSoapFault($sf);
        }
    
        return $contractProducts;
    }
}
