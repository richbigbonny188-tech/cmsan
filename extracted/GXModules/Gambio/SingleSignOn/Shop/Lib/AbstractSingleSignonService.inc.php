<?php
/* --------------------------------------------------------------
   AbstractSingleSignonService.inc.php 2020-05-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;

abstract class AbstractSingleSignonService implements LoggerAwareInterface
{
    const PASSWORD_ENTROPY_SIZE = 1024;
    
    protected $clientId;
    protected $clientSecret;
    protected $redirectUri;

    /** @var LoggerInterface */
    protected $logger;
    protected $loggerSubsystem = '';
    
    public function __construct($clientId, $clientSecret, $redirectUri)
    {
        $this->clientId     = $clientId;
        $this->clientSecret = $clientSecret;
        $this->redirectUri  = $redirectUri;
    }
    
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }
    
    protected function getLogger(): LoggerInterface
    {
        if ($this->logger === null) {
            $this->logger = MainFactory::create('SingleSignonLogger', true, $this->loggerSubsystem);
        }
        
        return $this->logger;
    }
    
    
    abstract public function getAuthorizationLink();
    
    
    abstract public function processAuthorizationCode($code);
    
    
    /**
     * Extracts claims data from encoded JWT data.
     *
     * Returns an array:
     * Array
     * (
     *   [azp] => 123412341234123412341234123412341234123411234.apps.googleusercontent.com
     *   [aud] => 123412341234123412341234123412341234123412344.apps.googleusercontent.com
     *   [sub] => 111112222233334445555
     *   [email] => john.doe@example.com
     *   [email_verified] => 1
     *   [at_hash] => dunegldnuditJDTRENia
     *   [iss] => https://accounts.google.com
     *   [iat] => 1500553894
     *   [exp] => 1500557494
     *   [name] => John Doe
     *   [picture] => https://lh3.googleusercontent.com/somewhere/photo.jpg
     *   [given_name] => John
     *   [family_name] => Doe
     *   [locale] => de
     * )
     *
     * @param $idToken
     *
     * @return array|mixed
     */
    protected function extractIdToken($idToken)
    {
        [$jwtHeadB64, $jwtClaimsB64, $jwtSignature] = explode('.', $idToken);
        $idTokenJson = base64_decode($jwtClaimsB64);
        $idTokenData = json_decode($idTokenJson, true);
        
        return $idTokenData;
    }
    
    
    protected function determineCountryByLocale($locale = '')
    {
        $countryId = STORE_COUNTRY;
        if (!empty($locale)) {
            if (preg_match('/^[a-z]{2}.[A-Z]{2}$/', $locale) === 1) {
                $language    = substr($locale, 0, 2);
                $countryIso2 = substr($locale, 3, 2);
            } elseif (preg_match('/^[A-Z]{2}$/', $locale) === 1) {
                $countryIso2 = $locale;
            } elseif (preg_match('/^[a-z]{2}$/', $locale) === 1) {
                $countryIso2 = strtoupper($locale);
            }
            if (!empty($countryIso2)) {
                $countryService = StaticGXCoreLoader::getService('Country');
                $country        = $countryService->getCountryByIso2($countryIso2);
                $countryId      = $country->getId();
            }
        }
        
        return $countryId;
    }
    
    
    protected function generateRandomBytes($numBytes = 1024)
    {
        $bytes = false;
        if (function_exists('random_bytes')) {
            $bytes = random_bytes($numBytes);
        } elseif (function_exists('openssl_random_pseudo_bytes')) {
            $strongCrypto = false;
            $bytes        = openssl_random_pseudo_bytes(1024, $strongCrypto);
        }
        if (false === $bytes) {
            $bytes = '';
            for ($i = 0; $i < $numBytes; $i++) {
                $bytes[$i] = chr(mt_rand(0, 254));
            }
        }
        
        return $bytes;
    }
    
    
    protected function makePassword()
    {
        if (function_exists('random_bytes')) {
            $randomData = random_bytes(self::PASSWORD_ENTROPY_SIZE);
        } else {
            $randomData = openssl_random_pseudo_bytes(1024);
            if (empty($randomData)) {
                $randomData = uniqid('', true);
            }
        }
        
        $password = hash('sha256', $randomData);
        
        return $password;
    }
}
