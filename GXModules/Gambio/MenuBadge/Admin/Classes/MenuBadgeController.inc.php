<?php
/* --------------------------------------------------------------
   MenuBadgeController.inc.php 2023-02-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

use Gambio\AdminFeed\Services\ShopInformation\ShopInformationFactory;

/**
 * Class MenuBadgeController
 *
 * The MenuBadgeController is responsible for checking for the count of available updates in the Gambio Store
 */
class MenuBadgeController extends AdminHttpViewController
{
    const CHECK_FOR_UPDATES_INTERVAL = 60 * 60; // 1 hour
    
    
    /**
     * @return JsonHttpControllerResponse
     */
    public function actionAvailableUpdatesCount(): JsonHttpControllerResponse
    {
        $dataCache = DataCache::get_instance();
        
        $storeUpdatesCache = [
            'availableUpdatesCount'  => null,
            'lastCheckUnixTimestamp' => 0,
        ];
        
        if ($dataCache->key_exists('store_updates_cache', true)) {
            $storeUpdatesCache = $dataCache->get_data('store_updates_cache', true);
        }
        
        if (gm_get_conf('GAMBIO_STORE_IS_REGISTERED') === 'true'
            && gm_get_conf('GAMBIO_STORE_TOKEN')
            && (int)gm_get_conf('CHECK_FOR_GAMBIO_STORE_UPDATES')
            && time() - self::CHECK_FOR_UPDATES_INTERVAL > $storeUpdatesCache['lastCheckUnixTimestamp'] ?? 0) {
            $storeUpdatesCache['availableUpdatesCount']  = $this->requestAvailableUpdates();
            $storeUpdatesCache['lastCheckUnixTimestamp'] = time();
            
            $dataCache->set_data('store_updates_cache', $storeUpdatesCache, true);
        }
        
        return MainFactory::create('JsonHttpControllerResponse',
                                   ['availableUpdatesCount' => $storeUpdatesCache['availableUpdatesCount']]);
    }
    
    
    /**
     * Try to request the count of available updates in the Gambio Store. Returns null, if request fails.
     *
     * @return int|null
     */
    protected function requestAvailableUpdates(): ?int
    {
        $body = ['shopInformation' => $this->getShopInformation()];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.store.gambio.com/a/merchant_modules');
        curl_setopt($ch, CURLOPT_TIMEOUT, 2);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json;charset=UTF-8',
            'x-store-token: ' . gm_get_conf('GAMBIO_STORE_TOKEN'),
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response    = curl_exec($ch);
        $errorNumber = curl_errno($ch);
        curl_close($ch);
        
        if ($errorNumber !== 0) {
            return null;
        }
        
        $jsonResponse = @json_decode($response, true);
        
        return isset($jsonResponse['updates']) ? count($jsonResponse['updates']) : null;
    }
    
    
    /**
     * @return array
     */
    protected function getShopInformation(): array
    {
        $factory = new ShopInformationFactory();
        
        $service    = $factory->createService();
        $serializer = $factory->createShopInformationSerializer();
        
        return $serializer->serialize($service->getShopInformation());
    }
}
