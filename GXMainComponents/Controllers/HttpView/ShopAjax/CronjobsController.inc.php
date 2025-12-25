<?php
/* --------------------------------------------------------------
   CronjobsController.inc.php 2018-08-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class CronjobsController
 *
 * @extends    HttpViewController
 * @category   System
 * @package    HttpViewControllers
 */
class CronjobsController extends HttpViewController
{
    /**
     * Runs the cronjob tasks.
     *
     * @return \JsonHttpControllerResponse
     */
    public function actionRun()
    {
        if ($this->_getQueryParameter('token') !== LogControl::get_secure_token()) {
            $response = [
                'success' => false
            ];
            
            http_response_code(401);
            
            return MainFactory::create('JsonHttpControllerResponse', $response, ['Content-Type: application/json']);
        }
        
        $taskService = CronjobServiceFactory::createTaskService();
        $taskService->run();
        
        $response = [
            'success' => true
        ];
        
        return MainFactory::create('JsonHttpControllerResponse', $response, ['Content-Type: application/json']);
    }
}