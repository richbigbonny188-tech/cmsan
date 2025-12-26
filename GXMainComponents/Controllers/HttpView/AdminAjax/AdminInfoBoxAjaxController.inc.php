<?php
/* --------------------------------------------------------------
   AdminInfoBoxAjaxController.php 2019-07-23
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class AdminInfoBoxAjaxController
 *
 * This class handles incoming ajax requests for the admin info box.
 *
 * @category   System
 * @package    AdminHttpViewControllers
 * @extends    AdminHttpViewController
 */
class AdminInfoBoxAjaxController extends AdminHttpViewController
{
    /**
     * Customer ID.
     * @var int
     */
    protected $customerId;
    
    /**
     * Language code.
     * @var LanguageCode
     */
    protected $languageCode;
    
    /**
     * Language ID.
     * @var int
     */
    protected $languageId;
    
    /**
     * Language text manager.
     * @var LanguageTextManager
     */
    protected $languageTextManager;
    
    /**
     * Formatting pattern for date time values.
     * @var string
     */
    protected $dateTimeFormat = 'Y-m-d H:i:s';
    
    /**
     * Admin Info Box Service.
     * @var InfoBoxService
     */
    protected $service;
    
    
    /**
     * Initializes the controller.
     */
    public function init()
    {
        $languageProvider = MainFactory::create('LanguageProvider', StaticGXCoreLoader::getDatabaseQueryBuilder());
        
        $this->customerId          = $_SESSION['customer_id'];
        $this->service             = StaticGXCoreLoader::getService('InfoBox');
        $this->languageId          = (int)($_SESSION['languages_id'] ?? null);
        $this->languageTextManager = MainFactory::create('LanguageTextManager', 'messages', $this->languageId);
        $this->languageCode        = $languageProvider->getCodeById(new IdType((int)$this->languageId));
    }
    
    
    /**
     * Checks if the customer is the admin.
     *
     * @return bool Is the customer the admin?
     */
    protected function _isAdmin()
    {
        try {
            $this->validateCurrentAdminStatus();
            
            return true;
        } catch (LogicException $exception) {
            return false;
        }
    }
    
    
    /**
     * Callback method for the default action.
     * @return JsonHttpControllerResponse
     */
    public function actionDefault()
    {
        return MainFactory::create('JsonHttpControllerResponse', []);
    }
    
    
    /**
     * Returns all messages.
     * @return JsonHttpControllerResponse
     * @throws AuthenticationException If the customer has no admin privileges.
     */
    public function actionGetAllMessages()
    {
        if (!$this->_isAdmin()) {
            throw new AuthenticationException('No admin privileges. Please contact the administrator.');
        }
        
        $this->_checkResetCacheToken();
        
        $collection = $this->service->getAllMessages()->getArray();
        
        $messages = [];
        
        /**
         * @var InfoBoxMessage $item
         */
        foreach ($collection as $item) {
            $message = [
                'id'               => $item->getId(),
                'source'           => $item->getSource(),
                'identifier'       => $item->getIdentifier(),
                'status'           => $item->getStatus(),
                'type'             => $item->getType(),
                'visibility'       => $item->getVisibility(),
                'buttonLink'       => $item->getButtonLink(),
                'customerId'       => $item->getCustomerId(),
                'addedDateTime'    => $item->getAddedDateTime()->format($this->dateTimeFormat),
                'modifiedDateTime' => $item->getModifiedDateTime()->format($this->dateTimeFormat),
                'headline'         => $item->getHeadLine($this->languageCode),
                'message'          => $item->getMessage($this->languageCode),
                'buttonLabel'      => $item->getButtonLabel($this->languageCode)
            ];
            
            $messages[] = $message;
        }
        
        return MainFactory::create('JsonHttpControllerResponse', $messages);
    }
    
    
    /**
     * Adds a new message.
     * @return JsonHttpControllerResponse
     * @throws AuthenticationException If the customer has no admin privileges.
     */
    public function actionAddMessage()
    {
        if (!$this->_isAdmin()) {
            throw new AuthenticationException('No admin privileges. Please contact the administrator.');
        }
        
        $message = MainFactory::create('InfoBoxMessage');
        
        try {
            $message->setSource(new StringType($this->_getQueryParameter('source')))
                ->setIdentifier(new StringType($this->_getQueryParameter('identifier')))
                ->setStatus(new StringType($this->_getQueryParameter('status')))
                ->setType(new StringType($this->_getQueryParameter('type')))
                ->setVisibility(new StringType($this->_getQueryParameter('visibility')))
                ->setButtonLink(new StringType($this->_getQueryParameter('buttonLink') ? : ''))
                ->setCustomerId(new IdType((int)$this->_getQueryParameter('customerId')))
                ->setMessage(new StringType($this->_getQueryParameter('message')),
                             $this->languageCode)
                ->setHeadLine(new StringType($this->_getQueryParameter('headline')), $this->languageCode)
                ->setButtonLabel(new StringType($this->_getQueryParameter('buttonLabel') ? : ''),
                                 $this->languageCode);
            
            $this->service->addMessage($message);
            
            return MainFactory::create('JsonHttpControllerResponse', ['success']);
        } catch (Exception $exception) {
            return MainFactory::create('JsonHttpControllerResponse', ['error']);
        }
    }
    
    
    /**
     * Adds a new success message.
     * @return JsonHttpControllerResponse
     * @throws AuthenticationException If the customer has no admin privileges.
     */
    public function actionAddSuccessMessage()
    {
        $messageSource      = 'adminAction';
        $messageIdentifier  = uniqid('adminActionSuccess-', true);
        $messageStatus      = 'new';
        $messageType        = 'success';
        $messageVisibility  = 'removable';
        $messageButtonLink  = '';
        $messageText        = $this->_getQueryParameter('message') ? : $this->languageTextManager->get_text('GM_LANGUAGE_CONFIGURATION_SUCCESS',
                                                                                                            'languages');
        $messageHeadLine    = $this->languageTextManager->get_text('success');
        $messageButtonLabel = '';
        
        if (!$this->_isAdmin()) {
            throw new AuthenticationException('No admin privileges. Please contact the administrator.');
        }
        
        $message = MainFactory::create('InfoBoxMessage');
        
        try {
            $message->setSource(new StringType($messageSource))
                ->setIdentifier(new StringType($messageIdentifier))
                ->setStatus(new StringType($messageStatus))
                ->setType(new StringType($messageType))
                ->setVisibility(new StringType($messageVisibility))
                ->setButtonLink(new StringType($messageButtonLink))
                ->setCustomerId(new IdType((int)$this->customerId))
                ->setMessage(new StringType($messageText), $this->languageCode)
                ->setHeadLine(new StringType($messageHeadLine), $this->languageCode)
                ->setButtonLabel(new StringType($messageButtonLabel), $this->languageCode);
            
            $this->service->addMessage($message);
            
            return MainFactory::create('JsonHttpControllerResponse', ['success']);
        } catch (Exception $exception) {
            return MainFactory::create('JsonHttpControllerResponse', ['error']);
        }
    }
    
    
    /**
     * Reactivates the messages.
     * @return JsonHttpControllerResponse
     * @throws AuthenticationException If the customer has no admin privileges.
     */
    public function actionReactivateMessages()
    {
        if (!$this->_isAdmin()) {
            throw new AuthenticationException('No admin privileges. Please contact the administrator.');
        }
        
        try {
            $this->service->reactivateMessages();
            
            return MainFactory::create('JsonHttpControllerResponse', ['success']);
        } catch (Exception $exception) {
            return MainFactory::create('JsonHttpControllerResponse', ['error']);
        }
    }
    
    
    /**
     * Deletes messages by their sources.
     * @return JsonHttpControllerResponse
     * @throws AuthenticationException If the customer has no admin privileges.
     */
    public function actionDeleteBySource()
    {
        if (!$this->_isAdmin()) {
            throw new AuthenticationException('No admin privileges. Please contact the administrator.');
        }
        
        try {
            $source = new StringType($this->_getQueryParameter('source'));
            $this->service->deleteMessageBySource($source);
            
            return MainFactory::create('JsonHttpControllerResponse', ['success']);
        } catch (Exception $exception) {
            return MainFactory::create('JsonHttpControllerResponse', ['error']);
        }
    }
    
    
    /**
     * Deletes messages by their identifiers.
     * @return JsonHttpControllerResponse
     * @throws AuthenticationException If the customer has no admin privileges.
     */
    public function actionDeleteByIdentifier()
    {
        if (!$this->_isAdmin()) {
            throw new AuthenticationException('No admin privileges. Please contact the administrator.');
        }
        
        try {
            $identifier = new StringType($this->_getQueryParameter('identifier'));
            $this->service->deleteMessageByIdentifier($identifier);
            
            return MainFactory::create('JsonHttpControllerResponse', ['success']);
        } catch (Exception $exception) {
            return MainFactory::create('JsonHttpControllerResponse', ['error']);
        }
    }
    
    
    /**
     * Delete a message by its ID.
     * @return JsonHttpControllerResponse
     * @throws AuthenticationException If the customer has no admin privileges.
     */
    public function actionDeleteById()
    {
        if (!$this->_isAdmin()) {
            throw new AuthenticationException('No admin privileges. Please contact the administrator.');
        }
        
        try {
            $id = new IdType($this->_getQueryParameter('id'));
            $this->service->deleteMessageById($id);
            
            return MainFactory::create('JsonHttpControllerResponse', ['success']);
        } catch (Exception $exception) {
            return MainFactory::create('JsonHttpControllerResponse', ['error']);
        }
    }
    
    
    /**
     * Sets a message status.
     *
     * @return JsonHttpControllerResponse
     * @throws AuthenticationException If the customer has no admin privileges.
     */
    public function actionSetMessageStatus()
    {
        if (!$this->_isAdmin()) {
            throw new AuthenticationException('No admin privileges. Please contact the administrator.');
        }
        
        try {
            $id     = new IdType($this->_getQueryParameter('id'));
            $status = new StringType($this->_getQueryParameter('status'));
            
            $this->service->setMessageStatus($id, $status);
            
            return MainFactory::create('JsonHttpControllerResponse', ['success']);
        } catch (Exception $exception) {
            return MainFactory::create('JsonHttpControllerResponse', ['error']);
        }
    }
    
    
    /**
     * Checks whether a reset-cache message is necessary.
     */
    protected function _checkResetCacheToken()
    {
        $cacheControl = MainFactory::create_object('CacheControl');
        
        $messages = $this->service->getAllMessages();
        
        $messageExists = false;
        
        /** @var InfoBoxMessage $message */
        foreach ($messages->getArray() as $message) {
            if ($message->getIdentifier() === 'clear_cache') {
                $messageExists = true;
                break;
            }
        }
        
        if ($cacheControl->reset_token_exists() && !$messageExists) {
            $message = MainFactory::create('InfoBoxMessage');
            
            $message->setSource(new StringType('internal'))
                ->setIdentifier(new StringType('clear_cache'))
                ->setStatus(new StringType('new'))
                ->setType(new StringType('info'))
                ->setVisibility(new StringType('alwayson'))
                ->setButtonLink(new StringType('request_port.php?module=ClearCache'))
                ->setCustomerId(new IdType((int)$_SESSION['customers_id']));
            
            $cidb             = StaticGXCoreLoader::getDatabaseQueryBuilder();
            $languageProvider = MainFactory::create('LanguageProvider', $cidb);
            foreach ($languageProvider->getCodes() as $languageCode) {
                $languageId          = $languageProvider->getIdByCode($languageCode);
                $languageTextManager = MainFactory::create_object('LanguageTextManager',
                                                                  [
                                                                      'admin_info_boxes',
                                                                      $languageId
                                                                  ]);
                $message->setMessage(new StringType($languageTextManager->get_text('TEXT_CLEAR_CACHE')), $languageCode)
                    ->setHeadLine(new StringType($languageTextManager->get_text('HEADLINE_CLEAR_CACHE')),
                                  $languageCode)
                    ->setButtonLabel(new StringType($languageTextManager->get_text('BUTTON_CLEAR_CACHE')),
                                     $languageCode);
            }
            
            $this->service->addMessage($message);
        }
    }
}