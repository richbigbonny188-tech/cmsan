<?php
/* --------------------------------------------------------------
   PayPalDataHubController.inc.php 2022-06-23
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class PayPalDataHubController extends HttpViewController
{
    protected $hubAuthenticated = false;
    
    
    public function proceed(HttpContextInterface $httpContext)
    {
        $serverData             = $this->httpContextReader->getServerData($httpContext);
        $clientKey              = (string)gm_get_conf('GAMBIO_HUB_CLIENT_KEY');
        $this->hubAuthenticated = !empty($clientKey)
                                  && !empty($serverData['HTTP_X_CLIENT_KEY'])
                                  && $clientKey === $serverData['HTTP_X_CLIENT_KEY'];
        parent::proceed($httpContext);
    }
    
    
    protected function makePermissionDeniedResponse()
    {
        $responseData = [
            'error' => [
                'message' => 'authentication failed',
            ],
        ];
        $headers      = [
            'HTTP/1.1 403 Permission denied',
        ];
        $response     = new JsonHttpControllerResponse($responseData, $headers);
        
        return $response;
    }
    
    
    public function actionDefault()
    {
        return $this->makePermissionDeniedResponse();
    }
    
    
    public function actionTrackingCodes()
    {
        if (!$this->hubAuthenticated) {
            return $this->makePermissionDeniedResponse();
        }
        
        $ordersId = (int)$this->_getQueryParameter('orders_id');
        /** @var CI_DB_query_builder $db */
        $db                  = StaticGXCoreLoader::getDatabaseQueryBuilder();
        $parcelTrackingCodes = $db->select('tracking_code, parcel_service_name, creation_date')
                                  ->from('orders_parcel_tracking_codes')
                                  ->where('order_id', $ordersId)
                                  ->get()
                                  ->result_array();
        $trackingCodes       = $parcelTrackingCodes;
        
        $headers = ['Content-Type: application/json'];
        
        return new JsonHttpControllerResponse($trackingCodes, $headers);
    }
    
}
