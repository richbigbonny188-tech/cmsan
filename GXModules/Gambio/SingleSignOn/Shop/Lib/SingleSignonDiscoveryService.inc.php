<?php
/* --------------------------------------------------------------
   SingleSignonDiscoveryService.inc.php 2017-09-25
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class SingleSignonDiscoveryService
{
    protected $discoveryUrl;
    protected $discoveryDocument;
    
    
    public function __construct($discoveryUrl)
    {
        $this->discoveryUrl = $discoveryUrl;
    }
    
    
    public function getIssuer()
    {
        $discoveryDocument = $this->retrieveDiscoveryDocument();
        
        return $discoveryDocument['issuer'];
    }
    
    
    public function getAuthorizationEndpoint()
    {
        $discoveryDocument = $this->retrieveDiscoveryDocument();
        
        return $discoveryDocument['authorization_endpoint'];
    }
    
    
    public function getTokenEndpoint()
    {
        $discoveryDocument = $this->retrieveDiscoveryDocument();
        
        return $discoveryDocument['token_endpoint'];
    }
    
    
    public function getUserinfoEndpoint()
    {
        $discoveryDocument = $this->retrieveDiscoveryDocument();
        
        return $discoveryDocument['userinfo_endpoint'];
    }
    
    
    public function getResponseTypesSupported()
    {
        $discoveryDocument = $this->retrieveDiscoveryDocument();
        
        return $discoveryDocument['response_types_supported'];
    }
    
    
    public function getScopesSupported()
    {
        $discoveryDocument = $this->retrieveDiscoveryDocument();
        
        return $discoveryDocument['scopes_supported'];
    }
    
    
    public function getClaimsSupported()
    {
        $discoveryDocument = $this->retrieveDiscoveryDocument();
        
        return $discoveryDocument['claims_supported'];
    }
    
    
    protected function retrieveDiscoveryDocument()
    {
        if (null === $this->discoveryDocument) {
            $restRequest               = MainFactory::create('RestRequest', 'GET', $this->discoveryUrl);
            $restService               = MainFactory::create('RestService');
            $discoveryDocumentResponse = $restService->performRequest($restRequest);
            if ($discoveryDocumentResponse->getResponseCode() !== 200) {
                throw new SingleSignonDiscoveryFailedException('OpenID discovery failed');
            }
            $discoveryDocument = json_decode($discoveryDocumentResponse->getResponseBody(), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new SingleSignonDiscoveryFailedException('invalid response');
            }
            $this->discoveryDocument = $discoveryDocument;
        }
        
        return $this->discoveryDocument;
    }
}
