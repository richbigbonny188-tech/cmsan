<?php

/* --------------------------------------------------------------
   PersonalDataController.inc.php 2018-05-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class PersonalDataController
 *
 * Controller to handle personal data
 *
 * @category System
 * @package  AdminHttpViewControllers
 */
class PersonalDataController extends AdminHttpViewController
{
    /**
     * Personal data service
     * @var PersonalDataService
     */
    protected $personalDataService;
    
    
    /**
     * Initialize the controller
     */
    public function init()
    {
        $this->personalDataService = StaticGXCoreLoader::getService('PersonalData');
    }
    
    
    /**
     * Delete the personal data of a customer
     * @return RedirectHttpControllerResponse Redirection to the customers overview page
     */
    public function actionDelete()
    {
        $this->personalDataService->deletePersonalDataByCustomerId($this->parsedCustomerId(),
                                                                   $this->parsedSelection());
        
        return MainFactory::create('RedirectHttpControllerResponse', FILENAME_CUSTOMERS);
    }
    
    
    /**
     * Export the personal data of a customer
     * @return FileDownloadHttpControllerResponse Redirection to the ZIP file download
     */
    public function actionExport()
    {
        $path = $this->personalDataService->exportPersonalDataByCustomerId($this->parsedCustomerId(),
                                                                           $this->parsedSelection());
        
        return MainFactory::create('FileDownloadHttpControllerResponse',
                                   new NonEmptyStringType($path),
                                   new NonEmptyStringType(basename($path)),
                                   new NonEmptyStringType('application/zip'));
    }
    
    
    /**
     * Return the parsed customer ID
     * @return IdType Parsed customer ID
     */
    protected function parsedCustomerId()
    {
        return new IdType($this->_getQueryParameter('id'));
    }
    
    
    /**
     * Return the parsed selection
     * @return PersonalDataSelection Parsed selection
     */
    protected function parsedSelection()
    {
        $selection = [
            PersonalDataSelectionItem::BASE_DATA                => $this->_getQueryParameter('base_data') === 'on',
            PersonalDataSelectionItem::ORDERS                   => $this->_getQueryParameter('orders') === 'on',
            PersonalDataSelectionItem::WITHDRAWALS              => $this->_getQueryParameter('withdrawals') === 'on',
            PersonalDataSelectionItem::AGREEMENTS               => $this->_getQueryParameter('agreements') === 'on',
            PersonalDataSelectionItem::EMAILS                   => $this->_getQueryParameter('emails') === 'on',
            PersonalDataSelectionItem::CARTS                    => $this->_getQueryParameter('carts') === 'on',
            PersonalDataSelectionItem::REVIEWS                  => $this->_getQueryParameter('reviews') === 'on',
            PersonalDataSelectionItem::NEWSLETTER_SUBSCRIPTIONS => $this->_getQueryParameter('newsletter_subscriptions')
                                                                   === 'on',
        ];
        
        return MainFactory::create('PersonalDataSelection', $selection);
    }
}