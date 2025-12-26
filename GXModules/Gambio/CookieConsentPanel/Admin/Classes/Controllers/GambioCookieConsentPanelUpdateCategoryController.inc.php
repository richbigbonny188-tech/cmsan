<?php
/* --------------------------------------------------------------
  GambioCookieConsentPanelUpdateCategoryTitleAndDescriptionController.php 2020-01-13
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2020 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

use Gambio\CookieConsentPanel\Storage\CookieConsentPanelStorage;

/**
 * Class GambioCookieConsentPanelUpdateCategoryTitleAndDescriptionController
 */
class GambioCookieConsentPanelUpdateCategoryController extends AbstractModuleCenterModuleController
{
    protected const REDIRECT_URL = 'admin.php?do=GambioCookieConsentPanelModuleCenterModule&activetab=categories';
    
    /**
     * @var CookieConsentPanelStorage
     */
    protected $storage;
    
    /**
     * @var CategoryFormNameToStorageKeyMapper
     */
    protected $mapper;
    
    
    /**
     * @return HttpControllerResponseInterface
     */
    public function actionDefault(): HttpControllerResponseInterface
    {
        $this->updateCategoryStrings();
        
        header('Location: ' . self::REDIRECT_URL);
        
        return new HttpControllerResponse('');
    }
    
    
    protected function updateCategoryStrings(): void
    {
        $keys = $this->mapper->formNamesToDatabaseKeys($_POST);
    
        foreach ($keys as $key => $value) {
            
            $this->storage->set($key, json_encode($value));
        }
    }
    
    
    /**
     * @inheritDoc
     */
    protected function _init()
    {
        $this->storage = new CookieConsentPanelStorage;
        $this->mapper  = new CategoryFormNameToStorageKeyMapper;
    }
}