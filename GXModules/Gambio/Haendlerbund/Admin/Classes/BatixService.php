<?php
/* --------------------------------------------------------------
   BatixService.php 2022-09-02
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);

namespace GXModules\Gambio\Haendlerbund\Admin\Classes;

use Gambio\Core\Configuration\Services\ConfigurationFinder;
use GuzzleHttp\Client;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\TransferException;
use GXModules\Gambio\Haendlerbund\Admin\Classes\Exceptions\BatixServiceException;
use Psr\Http\Message\ResponseInterface;

class BatixService
{
    
    /**
     * @var HaendlerbundConfigurationFinder
     */
    private $haendlerbundConfigurationFinder;
    
    private $client;
    
    public const DID_TOS_B2C                  = '12766C46A8A';
    public const DID_TOS_B2B                  = '14196AFA8F0';
    public const DID_WITHDRAWAL               = '12766C53647';
    public const DID_WITHDRAWAL_DIGITAL_GOODS = '1452C24576D';
    public const DID_WITHDRAWAL_SERVICES      = '1463C5DBF05';
    public const DID_PAYMENT_AND_SHIPPING_B2C = '12766C58F26';
    public const DID_PAYMENT_AND_SHIPPING_B2B = '1419BEA3135';
    public const DID_PRIVACY                  = '160DEDA9674';
    public const DID_IMPRINT                  = '1293C20B491';
    public const DID_BATTERIES                = '134CBB4D101';
    
    protected const ENDPOINT_BATIX_PRODUCTIVE       = 'https://www.hb-intern.de/www/hbm/api/live_rechtstexte.htm';
    protected const ENDPOINT_BATIX_DEVELOP          = 'https://dev-hb-intern.haendlerbund.de/www/hbm/api/live_rechtstexte.htm';
    protected const ENDPOINT_BATIX_CACHE_PRODUCTIVE = 'https://legaltext-cache.haendlerbund.de/cache';
    protected const ENDPOINT_BATIX_CACHE_DEVELOP    = 'https://develop.legaltext-cache.outlaws.dev-hb.de/cache';
    
    
    /**
     * @param HaendlerbundConfigurationFinder $haendlerbundConfigurationFinder
     */
    public function __construct(HaendlerbundConfigurationFinder $haendlerbundConfigurationFinder)
    {
        $this->haendlerbundConfigurationFinder = $haendlerbundConfigurationFinder;
        $baseUri      = $this->getEndpoint();
        $this->client = new Client([
                                       'base_uri' => $baseUri,
                                       'timeout'  => 5.0,
                                   ]);
    }
    
    
    /**
     * @return string
     */
    public function getApiKey(): string
    {
        $apiKeyConfig = $this->haendlerbundConfigurationFinder->get('apiKey');
        return $apiKeyConfig;
    }
    
    
    /**
     * @return string
     */
    protected function getEndpoint(): string
    {
        $mode = $this->haendlerbundConfigurationFinder->get('mode');
        if ($mode === 'develop') {
            return static::ENDPOINT_BATIX_DEVELOP;
        }
        return self::ENDPOINT_BATIX_CACHE_PRODUCTIVE;
    }
    
    
    /**
     * @param string $mode
     * @param string $did
     * @param string $lang
     *
     * @return ResponseInterface
     * @throws BatixServiceException
     */
    protected function performRequest(string $mode, string $did, string $lang = 'de'): ResponseInterface
    {
        $params = [
            'APIkey'      => $this->haendlerbundConfigurationFinder->get('apiKey'),
            'AccessToken' => $this->haendlerbundConfigurationFinder->get('accessToken'),
            'mode'        => $mode,
            'did'         => $did,
            'lang'        => $lang,
        ];
        
        try {
            return $this->client->get('', ['query' => $params]);
        } catch (TransferException $transferException) {
            throw new BatixServiceException('Network error: ' . $transferException->getMessage());
        }
    }
    
    
    /**
     * @param string $language
     *
     * @return array
     * @throws BatixServiceException
     */
    public function getDocuments(string $language = 'de'): array
    {
        $response = $this->performRequest('documents', '', $language);
        if($response->getStatusCode() === 200) {
            $documents = json_decode($response->getBody()->getContents(), true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $documents;
            }
            throw new BatixServiceException('Response could not be parsed');
        }
        throw new BatixServiceException("Error retrieving documents ({$response->getStatusCode()})");
    }
    
    
    /**
     * @param string $did
     *
     * @return bool
     * @throws BatixServiceException
     */
    public function checkDocumentAvailability(string $did): bool
    {
        $response = $this->performRequest('test', $did);
    
        return $response->getStatusCode() === 200 && $response->getBody()->getContents() === 'DOCUMENT_AVAILABLE';
    }
    
    
    /**
     * Returns languages available for a DID.
     * 
     * Note that, by convention, German (de) is always available.
     * 
     * @param string $did
     *
     * @return array
     * @throws BatixServiceException
     */
    public function getAvailableLanguages(string $did): array
    {
        $languages = ['de'];
        $response = $this->performRequest('trans', $did);
        if ($response->getStatusCode() === 200) {
            $translations = json_decode($response->getBody()->getContents(), true);
            if (json_last_error() === JSON_ERROR_NONE) {
                if (is_array($translations)) {
                    $languages = array_merge($languages, $translations);
                }
                return $languages;
            }
            throw new BatixServiceException('Response could not be parsed (invalid JSON)');
        }
        throw new BatixServiceException('Request could not be processed - check credentials!');
    }
    
    public function getDocumentMD5(string $did, string $language = 'de'): string
    {
        return '';
    }
    
    public function getDocumentDefault(string $did, string $language = 'de'): string
    {
        return $this->getDocument($did, 'default', $language);
    }
    
    public function getDocumentHtml(string $did, string $language = 'de'): string
    {
        return $this->getDocument($did, 'html', $language);
    }
    
    public function getDocumentClasses(string $did, string $language = 'de'): string
    {
        return $this->getDocument($did, 'classes', $language);
    }
    
    public function getDocumentPlain(string $did, string $language = 'de'): string
    {
        return $this->getDocument($did, 'plain', $language);
    }
    
    
    /**
     * @param string $did
     * @param string $type
     * @param string $language
     *
     * @return string
     * @throws BatixServiceException
     */
    protected function getDocument(string $did, string $type = 'default', string $language = 'de'): string
    {
        $validTypes = ['default', 'html', 'classes', 'plain'];
        $mode = in_array($type, $validTypes) ? $type : 'default';
        
        $documentResponse = $this->performRequest($mode, $did, $language);
        if ($documentResponse->getStatusCode() === 200) {
            return $documentResponse->getBody()->getContents();
        }
        throw new BatixServiceException("Could not retrieve document, check credentials\n" . $documentResponse->getBody()->getContents());
    }
    
}