<?php
/* --------------------------------------------------------------
   GoogleSingleSignonService.inc.php 2020-05-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class GoogleSingleSignonService extends AbstractSingleSignonService
{
    protected $loggerSubsystem = 'google';

    const DISCOVERY_URL = 'https://accounts.google.com/.well-known/openid-configuration';
    
    protected $discoveryService;
    
    
    public function __construct($clientId, $clientSecret, $redirectUri)
    {
        parent::__construct($clientId, $clientSecret, $redirectUri);
        $this->discoveryService = new SingleSignonDiscoveryService(self::DISCOVERY_URL);
    }
    
    
    public function getAuthorizationLink()
    {
        $scope                             = implode(' ', $this->discoveryService->getScopesSupported());
        $authorizationEndpoint             = $this->discoveryService->getAuthorizationEndpoint();
        $state                             = sha1($this->generateRandomBytes(1024));
        $_SESSION['google_oauth_states']   = isset($_SESSION['google_oauth_states']) ? $_SESSION['google_oauth_states'] : [];
        $_SESSION['google_oauth_states'][] = $state;
        $_SESSION['google_oauth_states']   = array_slice($_SESSION['google_oauth_states'], -10);
        $loginUrl                          = $authorizationEndpoint . '?' . http_build_query([
                                                                                                 'client_id'     => $this->clientId,
                                                                                                 'response_type' => 'code',
                                                                                                 'scope'         => $scope,
                                                                                                 'redirect_uri'  => $this->redirectUri,
                                                                                                 'state'         => $state,
                                                                                             ]);
        
        return $loginUrl;
    }
    
    
    public function processAuthorizationCode($code)
    {
        $accessTokenResponse = $this->retrieveTokensByCode($code);
        $idToken             = $this->extractIdToken($accessTokenResponse['id_token']);
        $countryId           = $this->determineCountryByLocale($idToken['locale']);
        
        $password = $this->makePassword();
        $ssoData  = [
            'iss'                 => $idToken['iss'],
            'sub'                 => $idToken['sub'],
            'customer_collection' => [
                'firstname'              => $idToken['given_name'],
                'lastname'               => $idToken['family_name'],
                'email_address'          => $idToken['email'],
                'email_address_confirm'  => $idToken['email'],
                'email_address_verified' => $idToken['email_verified'],
                'country'                => $countryId,
                'password'               => $password,
                'confirmation'           => $password,
            ],
            'identity'            => $idToken,
        ];
        
        return $ssoData;
    }
    
    
    /**
     * Uses an authorization code to retrieve access_token and id_token.
     *
     * The array returned should contain keys access_token, token_type, expires_in and id_token.
     * access_token and id_token are in JSON Web Token format; use extractIdToken() to extract claims data from
     * id_token. The token_type is always “Bearer”, and expires_in is given in seconds.
     *
     * @param $code
     *
     * @return array|mixed
     * @throws Exception
     */
    protected function retrieveTokensByCode($code)
    {
        $tokenEndpoint    = $this->discoveryService->getTokenEndpoint();
        $tokenRequestData = http_build_query([
                                                 'code'          => $code,
                                                 'client_id'     => $this->clientId,
                                                 'client_secret' => $this->clientSecret,
                                                 'redirect_uri'  => $this->redirectUri,
                                                 'grant_type'    => 'authorization_code',
                                             ]);
        
        $tokenRequest  = MainFactory::create('RestRequest', 'POST', $tokenEndpoint, $tokenRequestData);
        $restService   = MainFactory::create('RestService');
        $tokenResponse = $restService->performRequest($tokenRequest);
        $tokensData    = json_decode($tokenResponse->getResponseBody(), true);
        
        if (!empty($tokensData['error'])) {
            throw new Exception($tokensData['error'] . ' ' . $tokensData['error_description']);
        }
        
        return $tokensData;
    }
}
