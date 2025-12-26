<?php
/* --------------------------------------------------------------
   AmazonSingleSignonService.inc.php 2021-09-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class AmazonSingleSignonService extends AbstractSingleSignonService
{
    protected $mode = 'live';
    protected $loggerSubsystem = 'amazon';
    
    public function getAuthorizationLinkPage(): string
    {
        if ($_SESSION['sso_checkout_started'] === true) {
            $_SESSION['payment'] = 'amazonadvpay';
        }
        $jsSrc = 'https://static-eu.payments-amazon.com/OffAmazonPayments/eur/lpa/js/Widgets.js';
        $sandboxMode = 'false';
        if ($this->mode === 'sandbox') {
            $jsSrc = 'https://static-eu.payments-amazon.com/OffAmazonPayments/eur/sandbox/lpa/js/Widgets.js';
            $sandboxMode = 'true';
        }
    
        $this->getLogger()->debug('Redirecting user via Javascript');
        
        return <<<EOF
<!DOCTYPE html>
<html lang="en">
<head><title>Redirection</title></head>
<body>
    <div>
        <p>Please wait for authentication to start</p>
    </div>
    <script>
        window.onAmazonLoginReady = function() {
            amazon.Login.setClientId("{$this->clientId}");
            amazon.Login.setUseCookie(true);
            amazon.Login.setRegion(amazon.Login.Region.Europe);
            amazon.Login.setSandboxMode({$sandboxMode});
            amazon.Login.setLanguage("de-DE");
        };
        window.onAmazonPaymentsReady = function() {
            var loginOptions = {
                response_type: 'code',
                scope: 'profile postal_code payments:widget payments:shipping_address payments:billing_address',
                popup: false
            };
            amazon.Login.authorize(loginOptions, '{$this->redirectUri}');
        };
        
    </script>
    <script async='async' src='{$jsSrc}'></script>
</body>
</html>
EOF;
    }
    
    public function getAuthorizationLink()
    {
        return '';
    }
    
    
    public function processAuthorizationCode($code)
    {
        $this->getLogger()->debug('processing authorization code {code}', ['code' => $code]);
        $tokenEndpoint    = 'https://api.amazon.com/auth/o2/token';
        $tokenRequestData = [
            'grant_type'    => 'authorization_code',
            'code'          => $code,
            'redirect_uri'  => $this->redirectUri,
            'client_id'     => $this->clientId,
            'client_secret' => $this->clientSecret,
        ];
        $tokenRequest     = MainFactory::create('RestRequest', 'POST', $tokenEndpoint, $tokenRequestData);
        $tokenRestService = MainFactory::create('RestService');
        $tokenResponse    = $tokenRestService->performRequest($tokenRequest);
        $this->getLogger()->debug('token result: {result}', ['result' => (string)$tokenResponse->getResponseCode()]);
        $tokensData = json_decode($tokenResponse->getResponseBody(), true);
        
        if (!empty($tokensData['error']) || empty($tokensData['access_token'])) {
            throw new Exception($tokensData['error'] . ' - ' . $tokensData['error_description']);
        }
        
        $profileUrl            = 'https://api.amazon.com/user/profile';
        $profileRequestHeaders = [
            'Authorization: Bearer ' . $tokensData['access_token'],
        ];
        $profileRequest        = MainFactory::create('RestRequest', 'GET', $profileUrl, [], $profileRequestHeaders);
        $profileResponse       = $tokenRestService->performRequest($profileRequest);
        $this->getLogger()->debug('profile result: {result}', ['result' => $profileResponse->getResponseCode()]);
        $profileData = json_decode($profileResponse->getResponseBody(), true);
        
        $addressVerified = true;
        
        setcookie('amazon_Login_accessToken', $tokensData['access_token'], 0, '/', '', true);
        setcookie('amazon_Login_state_cache', '');
        
        $password = $this->makePassword();
        
        $ssoData = [
            'iss'                 => 'amazon.com',
            'sub'                 => $profileData['user_id'],
            'customer_collection' => [
                'email_address'          => $profileData['email'],
                'email_address_confirm'  => $profileData['email'],
                'email_address_verified' => $addressVerified,
                'password'               => $password,
                'confirmation'           => $password,
            ],
            'tokens'              => $tokensData,
            'access_token'        => $tokensData['access_token'],
            'identity'            => $profileData,
        ];
        
        return $ssoData;
    }
    
    
    /**
     * Sets operation mode.
     *
     * Valid modes are 'sandbox' and 'live'. Default is 'live'.
     *
     * @param $mode
     *
     * @throws UnsupportedPaypalModeException
     */
    public function setMode($mode)
    {
        if (!in_array($mode, ['live', 'sandbox'], true)) {
            throw new UnsupportedPaypalModeException('mode unsupported: ' . $mode);
        }
        $this->mode = $mode;
    }
    
}
