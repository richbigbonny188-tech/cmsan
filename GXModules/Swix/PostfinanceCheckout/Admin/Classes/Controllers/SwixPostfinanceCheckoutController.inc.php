<?php
/*--------------------------------------------------------------------------------------------------
    SwixPostfinanceCheckoutController.inc.php 2021-07-27
    swisswebXperts GmbH
    https://www.swisswebxperts.ch
    Copyright (c) 2021 swisswebXperts GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

use SwixPostfinanceCheckout\ApiClient;

class SwixPostfinanceCheckoutController extends AdminHttpViewController
{
    /**
     * @var NonEmptyStringType
     */
    protected $title;

    /**
     * @var string
     */
    protected $jsBaseUrl;

    /**
     * @var string
     */
    protected $stylesBaseUrl;

    /**
     * @var string
     */
    protected $templatesBaseUrl;

    /**
     * @var LanguageTextManager
     */
    protected $languageTextManager;

    /**
     * @var CI_DB_query_builder
     */
    protected $db;

    /**
     * @var AssetCollection
     */
    protected $assets;

    protected $userId;
    protected $authenticationKey;
    protected $spaceId;

    protected $webhookUrlName = 'Gambio %s';
    protected $webhookListenerName = 'Gambio %s';

    public function init()
    {
        $this->languageTextManager  = MainFactory::create('LanguageTextManager',
            'swixpostfinancecheckout',
            $_SESSION['languages_id']);

        /** @var GXModuleConfigurationStorage $configurationStorage */
        $configurationStorage = MainFactory::create('GXModuleConfigurationStorage', 'Swix/PostfinanceCheckout');

        $this->webhookUrlName = substr(sprintf($this->webhookUrlName, $_SERVER['HTTP_HOST']), 0, 50);
        $this->webhookListenerName = substr(sprintf($this->webhookListenerName, $_SERVER['HTTP_HOST']), 0, 50);

        $this->userId = $configurationStorage->get('user_id') !== false ? $configurationStorage->get('user_id'): '';
        $this->authenticationKey = $configurationStorage->get('authentication_key') !== false ? $configurationStorage->get('authentication_key'): '';
        $this->spaceId = $configurationStorage->get('space_id') !== false ? $configurationStorage->get('space_id'): '';

        $this->title                = new NonEmptyStringType($this->languageTextManager->get_text('page_title'));
        $this->templatesBaseUrl     = DIR_FS_CATALOG . 'GXModules/Swix/PostfinanceCheckout/Admin/Html';
        $this->db                   = StaticGXCoreLoader::getDatabaseQueryBuilder();

        $assetsArray = [
            MainFactory::create('Asset', 'swispostfinancecheckout.lang.inc.php'),
        ];

        $this->assets = MainFactory::create('AssetCollection', $assetsArray);
    }

    public function actionDefault()
    {
        $template = new ExistingFile(new NonEmptyStringType($this->templatesBaseUrl . '/basic_configuration.html'));

        $configurationStorage = MainFactory::create('GXModuleConfigurationStorage', 'Swix/PostfinanceCheckout');
        $this->userId = $configurationStorage->get('user_id');
        $this->authenticationKey = $configurationStorage->get('authentication_key');
        $this->spaceId = $configurationStorage->get('space_id');

        $connected = false;
        $webhookExists = false;

        if (strlen($this->userId) > 0 && strlen($this->authenticationKey) > 0) {

            $apiClient = new ApiClient($this->userId, $this->authenticationKey);

            try {
                $space = $apiClient->getSpaceService()->read($this->spaceId);

                if ($space['id'] > 0) {
                    $connected = true;
                } else {
                    throw new \Exception('');
                }
            } catch (Exception $e) {
                $connected = false;
            }

            try {
                $query = [
                    'filter' => [
                        'children' => [
                            [
                                'fieldName' => 'name',
                                'operator' => 'EQUALS',
                                'type' => 'LEAF',
                                'value' => $this->webhookListenerName,
                            ],
                            [
                                'fieldName' => 'state',
                                'operator' => 'EQUALS',
                                'type' => 'LEAF',
                                'value' => 'ACTIVE',
                            ]
                        ],
                        'type' => 'AND'
                    ]
                ];
                $response = $apiClient->getWebhookListenerService()->search($this->spaceId, $query);

                if (is_array($response) && count($response) > 0) {
                    $webhookExists = true;
                } else {
                    throw new \Exception('');
                }

            } catch (Exception $e) {
                $webhookExists = false;
            }
        }

        $pageData = [
            'user_id' => $this->userId,
            'authentication_key' => $this->authenticationKey,
            'space_id' => $this->spaceId,
            'connected' => $connected,
            'webhookExists' => $webhookExists,
        ];

        $data = MainFactory::create('KeyValueCollection', $pageData);
        return MainFactory::create('AdminLayoutHttpControllerResponse',
            $this->title,
            $template,
            $data,
            $this->assets);
    }

    public function actionUpdateSettings()
    {
        /** @var GXModuleConfigurationStorage $configurationStorage */
        $configurationStorage = MainFactory::create('GXModuleConfigurationStorage', 'Swix/PostfinanceCheckout');
        $configurationStorage->set('user_id', xtc_db_input($_POST['user_id']));
        $configurationStorage->set('authentication_key', xtc_db_input($_POST['authentication_key']));
        $configurationStorage->set('space_id', xtc_db_input($_POST['space_id']));

        $this->userId = $configurationStorage->get('user_id');
        $this->authenticationKey = $configurationStorage->get('authentication_key');
        $this->spaceId = $configurationStorage->get('space_id');

        $apiClient = new ApiClient($this->userId, $this->authenticationKey);

        try {
            $space = $apiClient->getSpaceService()->read($this->spaceId);

            if (isset($space['id']) && $space['id'] === $this->spaceId) {
                $GLOBALS['messageStack']->add_session('Konfiguration gespeichert. Verbindung mit Postfinance Checkout konnte hergestellt werden.', 'success');
            } else {
                throw new Exception('');
            }
        } catch(Exception $e) {
            $GLOBALS['messageStack']->add_session('Bitte überprüfen Sie Ihre Daten es konnte keine Verbindung hergestellt werden.', 'error');
        }

        try {
            $entityQuery = [
                'filter' => [
                    'children' => [
                        [
                            'fieldName' => 'url',
                            'operator' => 'EQUALS',
                            'type' => 'LEAF',
                            'value' => GM_HTTP_SERVER . DIR_WS_CATALOG . 'callback/swixpostfinancecheckout/callback.php',
                        ],
                        [
                            'fieldName' => 'state',
                            'operator' => 'NOT_EQUALS',
                            'type' => 'LEAF',
                            'value' => 'DELETED',
                        ]
                    ],
                    'type' => 'AND'
                ]
            ];

            $response = $apiClient->getWebhookUrlService()->search($this->spaceId, $entityQuery);
            
            if (count($response) > 0) {
                $webhookUrl = $response[0];

                if ($webhookUrl['state'] !== 'ACTIVE' || $webhookUrl['name'] !== $this->webhookUrlName) {
                    $webhookUrl['state'] = 'ACTIVE';
                    $webhookUrl['name'] = $this->webhookUrlName;
                    $webhookUrl = $apiClient->getWebhookUrlService()->update($this->spaceId, $webhookUrl);
                }
            } else {
                $webhookUrl = [
                    'name' => $this->webhookUrlName,
                    'state' => 'ACTIVE',
                    'url' => GM_HTTP_SERVER . DIR_WS_CATALOG . 'callback/swixpostfinancecheckout/callback.php',
                ];

                $webhookUrl = $apiClient->getWebhookUrlService()->create($this->spaceId, $webhookUrl);
            }
        } catch(Exception $e) {
            $GLOBALS['messageStack']->add_session('Fehler beim erstellen der Webhook URL', 'error');
        }

        try {
            $entityQuery = [
                'filter' => [
                    'children' => [
                        [
                            'fieldName' => 'name',
                            'operator' => 'EQUALS',
                            'type' => 'LEAF',
                            'value' => $this->webhookListenerName,
                        ],
                        [
                            'fieldName' => 'state',
                            'operator' => 'NOT_EQUALS',
                            'type' => 'LEAF',
                            'value' => 'DELETED',
                        ]
                    ],
                    'type' => 'AND'
                ]
            ];

            $response = $apiClient->getWebhookListenerService()->search($this->spaceId, $entityQuery);

            if (!empty($response)) {
                $webhookListener = $response[0];

                if ($webhookListener['state'] != 'ACTIVE') {
                    $webhookListener['state'] = 'ACTIVE';
                    $webhookListener = $apiClient->getWebhookListenerService()->update($this->spaceId, $webhookListener);
                }
            } else {
                $webhookListener = [
                    'name' => $this->webhookListenerName,
                    'state' => 'ACTIVE',
                    'url' => $webhookUrl,
                    'entity' => 1472041829003,
                    'entityStates' => [
                        'FULFILL',
                        'DECLINE',
                    ],
                ];

                $webhookListener = $apiClient->getWebhookListenerService()->create($this->spaceId, $webhookListener);
            }

        } catch (Exception $e) {
            $GLOBALS['messageStack']->add_session('Fehler beim erstellen des Webhook Listeners.', 'error');
        }

        return MainFactory::create(RedirectHttpControllerResponse::class, DIR_WS_ADMIN . 'admin.php?do=SwixPostfinanceCheckout');
    }
}