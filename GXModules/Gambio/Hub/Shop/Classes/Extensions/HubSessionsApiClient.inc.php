<?php
/* --------------------------------------------------------------
   HubSessionsApiClient.inc.php 2018-12-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

use HubPublic\Http\CurlRequest;
use HubPublic\ValueObjects\AuthHash;

/**
 * Class HubSessionApiClient
 *
 * @category   System
 * @package    Extensions
 * @subpackage GambioHub
 */
class HubSessionsApiClient implements HubSessionsApiClientInterface
{
    /**
     * @var string
     */
    protected $url;

    /**
     * @var \HubSessionKeyService
     */
    protected $hubSessionKeyService;

    /**
     * @var \HubClientKeyConfiguration
     */
    protected $clientKeyConfiguration;

    /**
     * @var \HubPublic\Http\CurlRequest
     */
    protected $curlRequest;

    /**
     * @var \LogControl
     */
    protected $logControl;

    /**
     * @var integer
     */
    protected $secondsBetweenNotifications;

    /**
     * @var \HubSettings
     */
    protected $hubSettings;


    /**
     * HubSessionApiClient constructor.
     *
     * @param string                      $url                                       Gambio Hub API URL
     * @param \HubSessionKeyService       $hubSessionKeyService                      Used for retrieving the session
     *                                                                               key.
     * @param \HubClientKeyConfiguration  $hubClientKeyConfiguration                 Stores or retrieves the hub client
     *                                                                               key from shop configuration.
     * @param \HubPublic\Http\CurlRequest $curlRequest                               Make cURL requests to the Hub API.
     * @param \LogControl                 $logControl                                Log communication error
     *                                                                               information.
     * @param \HubSettings                $hubSettings                               Hub settings.
     *
     * @throws InvalidArgumentException If the $url argument is not a valid URL.
     */
    public function __construct(
        $url,
        HubSessionKeyService $hubSessionKeyService,
        HubClientKeyConfiguration $hubClientKeyConfiguration,
        CurlRequest $curlRequest,
        LogControl $logControl,
        HubSettings $hubSettings
    ) {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException('Invalid Gambio Hub API URL provided: ' . $url);
        }

        $this->url                         = $url;
        $this->clientKeyConfiguration      = $hubClientKeyConfiguration;
        $this->hubSessionKeyService        = $hubSessionKeyService;
        $this->curlRequest                 = $curlRequest;
        $this->logControl                  = $logControl;
        $this->hubSettings                 = $hubSettings;
        $this->secondsBetweenNotifications = 60 * 60 * 24;
    }


    /**
     * Checks the version_info folder for the latest connector version and returns it
     *
     * @return string
     */
    private function determineConnectorVersion()
    {
        $files = glob(DIR_FS_CATALOG . 'version_info/gambio_hub*.php');

        $gambioHubKeys = array_filter($files, function ($file) {
            return strpos($file, 'gambio_hub') !== false;
        });

        $gambioHubKeys = array_map(function ($gambioHubKey) {
            return str_replace(['gambio_hub-', '_', '.php'], ['', '.', ''], $gambioHubKey);
        }, $gambioHubKeys);

        usort($gambioHubKeys, function ($a, $b) {
            return version_compare($a, $b);
        });

        $lastGambioHubKey = basename(end($gambioHubKeys));

        return $lastGambioHubKey;
    }


    /**
     * Starts a session in the Gambio Hub.
     *
     * Provide an authorization hash that can be later used in the hub callbacks to determine where each
     * session key belongs to. This method will additionally save the AuthHash value to the PHP session with
     * the key 'gambio_hub_auth_hash' for later reference.
     *
     * @param \HubPublic\ValueObjects\AuthHash $authHash The authorization hash to be used for the session start.
     * @param string                           $shopUrl  Shop url with trailing slash.
     * @param \LanguageCode                    $languageCode
     *
     * @return string Returns the new session key.
     *
     * @throws UnexpectedValueException If the server responses with status code different to 201.
     */
    public function startSession(AuthHash $authHash, $shopUrl, LanguageCode $languageCode)
    {
        if (!is_string($shopUrl)) {
            throw new InvalidArgumentException('Shop url argument is not a string: ' . gettype($shopUrl));
        }

        $clientKey = $this->clientKeyConfiguration->get();

        $requestUrl = $this->url . '/hub_client_keys/' . $clientKey;

        // Set parameter request URL.
        $this->curlRequest->setUrl($requestUrl);

        // Set parameter request data.
        $this->curlRequest->setOption(CURLOPT_HTTPHEADER, [
            'Expect:',
            'X-Auth-Hash: ' . $authHash->asString(),
            'X-Shop-Key: ' . GAMBIO_SHOP_KEY,
            'X-Origin-Client-Url: ' . $shopUrl,
            'X-Language-Code: ' . strtolower($languageCode->asString()),
            'X-Connector-Version: ' . $this->determineConnectorVersion(),
        ]);

        // Set parameter request method to 'POST'.
        $this->curlRequest->setOption(CURLOPT_POST, true);

        // Set empty body to avoid bug in old curl lib versions
        $this->curlRequest->setOption(CURLOPT_POSTFIELDS, '');

        // Set timeout
        $this->curlRequest->setOption(CURLOPT_TIMEOUT, $this->hubSettings->getCurlTimeout());

        // Perform parameter request.
        $httpResponse = $this->curlRequest->execute();

        if ($httpResponse->getStatusCode() === 202) {
            $this->_sendUrlChangedNotification($clientKey);
        } elseif ($httpResponse->getStatusCode() !== 201) {
            $this->logControl->notice('Failed to start a new session. ' . $httpResponse->getBody(), '', 'hub', 'notice',
                'USER NOTICE', $httpResponse->getStatusCode());

            throw new UnexpectedValueException('Failed to start a new session. ' . $httpResponse->getBody() . ' ('
                                               . $httpResponse->getStatusCode() . ')');
        }

        $sessionKey = $this->hubSessionKeyService->findByAuthHash($authHash);

        if ($sessionKey === null) {
            throw new UnexpectedValueException('Failed to start a new session, because auth hash was not found or auth hash file content is empty.');
        }

        $_SESSION['gambio_hub_session_key'] = $sessionKey->asString();

        // reset session params used for hub session key refresh check in GambioHubPayment::selection
        $_SESSION['gambio_hub_session_key_refreshed'] = microtime(true);
        unset($_SESSION['gambio_hub_session_criteria']);

        return $_SESSION['gambio_hub_session_key'];
    }


    /**
     * Sends a request to the hub that the shop url could be changed.
     *
     * @param string $clientKey Client key of the shop.
     *
     * @return $this|\HubSessionsApiClient Same instance for chained method calls.
     * @throws \UnexpectedValueException Every time when the method is called.
     */
    protected function _sendUrlChangedNotification($clientKey)
    {
        $currentTimestamp    = time();
        $lastNotifyTimestamp = (int)gm_get_conf('LAST_SHOP_URL_NOTIFY');
        if (!isset($_SESSION['hubLastNotifyTimestamp'])) {
            $_SESSION['hubLastNotifyTimestamp'] = $lastNotifyTimestamp;
        }

        if ($currentTimestamp >= $lastNotifyTimestamp + $this->secondsBetweenNotifications
            && $currentTimestamp >= $_SESSION['hubLastNotifyTimestamp'] + $this->secondsBetweenNotifications) {
            $requestUrl = $this->url . '/hub_client_keys/' . $clientKey . '/notify_changed_url';

            $db    = StaticGXCoreLoader::getDatabaseQueryBuilder();
            $admin = $db->select('customers_gender')->from('customers')->where([
                'customers_firstname' => TRADER_FIRSTNAME,
                'customers_lastname'  => TRADER_NAME,
                'customers_status'    => 0
            ])->get()->row_array();

            $newClientUrl = HTTP_SERVER . DIR_WS_CATALOG;

            $data = [
                'clientKey'    => $clientKey,
                'newClientUrl' => $newClientUrl,
                // oldClientUrl is unknown at this point, but the hub knows more
                'email'        => CONTACT_US_EMAIL_ADDRESS,
                'gender'       => count($admin) > 0 ? $admin['customers_gender'] : '',
                'firstname'    => TRADER_FIRSTNAME,
                'lastname'     => TRADER_NAME,
                'languageCode' => $_SESSION['language_code']
            ];

            $this->curlRequest->setUrl($requestUrl);
            $this->curlRequest->setOption(CURLOPT_POST, true);
            $this->curlRequest->setOption(CURLOPT_POSTFIELDS, $data);

            // Set timeout
            $this->curlRequest->setOption(CURLOPT_TIMEOUT, $this->hubSettings->getCurlTimeout());

            $httpResponse = $this->curlRequest->execute();
            gm_set_conf('LAST_SHOP_URL_NOTIFY', $currentTimestamp);
            $_SESSION['hubLastNotifyTimestamp'] = $currentTimestamp;

            $this->logControl->notice('Failed to start a new session. Send url changed notification. '
                                      . $httpResponse->getBody(), '', 'hub', 'notice', 'USER NOTICE',
                $httpResponse->getStatusCode());
        } else {
            $this->logControl->notice('Failed to start a new session. Send no url changed notification, because of anti-spam mechanic.',
                '', 'hub', 'notice', 'USER NOTICE');
        }

        throw new UnexpectedValueException('Failed to start a new session. Send url changed notification.');
    }
}
