<?php
/* --------------------------------------------------------------
   GambioHubAjaxController.inc.php 2023-07-17
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class GambioHubController
 *
 * This class contains the Hub Web-API callbacks of shop, for versions that include the HttpService. For
 * legacy versions check the request_port.php and the respective AjaxHandler classes.
 */
class GambioHubAjaxController extends HttpViewController
{
    /**
     * @var LogControl
     */
    private $logControl;
    
    
    /**
     * Currently not implemented
     *
     * @return \JsonHttpControllerResponse
     */
    public function actionDefault()
    {
        
        $response = [
            'success' => false,
            'notice'  => 'Method not implemented'
        ];
        
        return new JsonHttpControllerResponse($response);
    }
    
    
    /**
     * Logs the browser log data to identify if a problem in the hub is caused from the browser.
     *
     */
    public function actionLogBrowserData()
    {
        if (($_SESSION['payment'] ?? null) !== 'gambio_hub') {
            return new JsonHttpControllerResponse(['success' => true]);
        }
        
        $this->setup();
        try {
            $this->logControl->notice(
                $_SESSION['payment'] . ' ' . ($_SESSION['gambio_hub_selection'] ?? ''),
                '',
                'browser_logs',
                'notice',
                'USER NOTICE',
                0,
                ''
            );
        } catch (Throwable $exception) {
            return new JsonHttpControllerResponse(['success' => false]);
        }
        
        return new JsonHttpControllerResponse(['success' => true]);
    }
    
    
    /**
     * Initializes the controller
     */
    private function setup()
    {
        $this->logControl = LogControl::get_instance();
    }
    
}

