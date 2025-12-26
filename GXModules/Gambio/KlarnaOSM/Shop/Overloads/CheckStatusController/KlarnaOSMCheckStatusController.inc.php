<?php
/* --------------------------------------------------------------
   KlarnaOSMCheckStatusController.inc.php 2022-08-17
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

use Gambio\Shop\SellingUnit\Unit\SellingUnit;

class KlarnaOSMCheckStatusController extends KlarnaOSMCheckStatusController_parent
{
    public function actionDefault()
    {
        /** @var JsonHttpControllerResponse $jsonHttpControllerResponse */
        $jsonHttpControllerResponse = parent::actionDefault();
        
        return $this->modifyResponse($jsonHttpControllerResponse);
    }
    
    
    public function actionAttributes()
    {
        /** @var JsonHttpControllerResponse $jsonHttpControllerResponse */
        $jsonHttpControllerResponse = parent::actionAttributes();
        
        return $this->modifyResponse($jsonHttpControllerResponse);
    }
    
    
    protected function modifyResponse(JsonHttpControllerResponse $jsonHttpControllerResponse)
    {
        include DIR_FS_CATALOG . 'release_info.php';
        if (version_compare(preg_replace('/(v?)(\d+\.\d+\.\d+\.\d+)(.*)/', '$2', $gx_version), '4.1.3.0', '<')) {
            return $jsonHttpControllerResponse;
        }
        
        if ($this->sellingUnit()->price()->status()->value() > 0) {
            return $jsonHttpControllerResponse;
        }
        
        if (!KlarnaOSMHelper::isModuleInstalledAndActive())
        {
            return $jsonHttpControllerResponse;
        }
        
        $jsonBody = $jsonHttpControllerResponse->getBody();
        $result   = json_decode($jsonBody, true);
        if (!is_array($result)) {
            return $jsonHttpControllerResponse;
        }
        
        /** @var SellingUnit $sellingUnit */
        $sellingUnit                         = $this->sellingUnit();
        $rawPrice                            = $sellingUnit->price()->pricePlain()->value();
        $kosmPrice                           = json_encode(['price' => $rawPrice * 100]);
        $result['content']['price']['value'] .= '<script id="kosm_price" type="application/json">' . $kosmPrice
                                                . '</script>';
        
        return MainFactory::create('JsonHttpControllerResponse', $result);
    }
    
}