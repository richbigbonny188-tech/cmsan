<?php
/* --------------------------------------------------------------
   CronjobAjaxController.inc.php 2018-08-28
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class CronjobAjaxController extends AdminHttpViewController
{
    /**
     * @var \LanguageTextManager
     */
    protected $languageTextManager;
    
    
    /**
     * Returns cronjobs information, including meta data like last execution and cronjob url.
     *
     * @return array|bool|\JsonHttpControllerResponse
     */
    public function actionGetCronjobs()
    {
        $service  = CronjobServiceFactory::createService();
        $cacheDir = new ExistingDirectory(DIR_FS_CATALOG . 'cache');
        
        return MainFactory::create(JsonHttpControllerResponse::class,
                                   array_merge_recursive($service->getAll()->toArray($cacheDir),
                                                         [
                                                             'meta' => [
                                                                 'cronjobUrl' => CronjobTaskService::getCronjobUrl()
                                                             ]
                                                         ]));
    }
    
    
    /**
     * Returns the log data of a cronjob task.
     *
     * @return array|bool|\JsonHttpControllerResponse
     */
    public function actionGetLogs()
    {
        $name    = new StringType($this->_getQueryParameter('task'));
        $cronjob = CronjobServiceFactory::createService()->getByName($name);
        $logDir  = new ExistingDirectory(DIR_FS_CATALOG . 'logfiles');
        
        try {
            return MainFactory::create(JsonHttpControllerResponse::class,
                                       ['success' => true, 'log' => $cronjob->getLog($logDir)]);
        } catch (CronjobLogFileNotFoundException $e) {
            return MainFactory::create(JsonHttpControllerResponse::class,
                                       ['success' => false, 'error' => $e->getMessage()]);
        } catch (\Exception $e) {
            $languageTextManager = MainFactory::create('LanguageTextManager', 'cronjobs');
            http_response_code(500);
            
            return MainFactory::create(JsonHttpControllerResponse::class,
                                       [
                                           'success' => false,
                                           'error'   => $languageTextManager->get_text('unexpected_error')
                                       ]);
        }
    }
    
    
    /**
     * Saves the cronjob configuration.
     *
     * @return \JsonHttpControllerResponse
     */
    public function actionSaveConfiguration()
    {
        try {
            $service = CronjobServiceFactory::createService();
            $task    = new StringType($this->_getQueryParameter('task'));
            $service->save($task, $this->_getPostDataCollection());
            
            return MainFactory::create(JsonHttpControllerResponse::class, ['success' => true]);
        } catch (\Exception $e) {
            return MainFactory::create(JsonHttpControllerResponse::class, ['success' => false]);
        }
    }
}