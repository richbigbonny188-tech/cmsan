<?php
/* --------------------------------------------------------------
	ShipcloudCarriersCache.inc.php 2024-02-12
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2024 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

class ShipcloudCarriersCache
{
    protected ?array          $shipcloudCarriers;
    protected ShipcloudLogger $shipcloudLogger;
    protected int             $maxCacheAge = 600;
    
    
    public function __construct()
    {
        $this->shipcloudCarriers = null;
        $this->shipcloudLogger   = MainFactory::create('ShipcloudLogger');
    }
    
    
    /**
     * Returns list of carrier names.
     *
     * @return array
     */
    public function getCarriers(): array
    {
        if ($this->shipcloudCarriers === null) {
            $cacheFile = DIR_FS_CATALOG . '/cache/shipcloud-carriers-' . LogControl::get_secure_token() . '.pdc';
            if (file_exists($cacheFile) && (int)filemtime($cacheFile) > (time() - $this->getMaxCacheAge())) {
                $this->shipcloudCarriers = unserialize(file_get_contents($cacheFile));
            } else {
                $this->shipcloudCarriers = $this->_retrieveShipcloudCarriers();
                if (!empty($this->shipcloudCarriers)) {
                    file_put_contents($cacheFile, serialize($this->shipcloudCarriers));
                }
            }
        }
        
        return $this->shipcloudCarriers ?? [];
    }
    
    
    protected function _retrieveShipcloudCarriers(): array
    {
        try {
            $restService      = MainFactory::create('ShipcloudRestService');
            $carriersRequest  = MainFactory::create('ShipcloudRestRequest', 'GET', '/v1/carriers');
            $carriersResponse = $restService->performRequest($carriersRequest);
            $carriers         = $carriersResponse->getResponseObject();
        } catch (Exception $e) {
            $this->shipcloudLogger->notice('ERROR - could not retrieve list of carriers: ' . $e->getMessage());
            $carriers = [];
        }
        if ($carriers === false) $carriers = [];
        
        return $carriers;
    }
    
    
    public function getCarrier(string $carrierName): ?object
    {
        $carrier = null;
        foreach ($this->getCarriers() as $shipcloudCarrier) {
            if ($shipcloudCarrier->name === $carrierName) {
                $carrier = $shipcloudCarrier;
            }
        }
        
        return $carrier;
    }
    
    
    /**
     * @return int
     */
    public function getMaxCacheAge(): int
    {
        return $this->maxCacheAge;
    }
    
    
    /**
     * @param int $maxCacheAge
     */
    public function setMaxCacheAge(int $maxCacheAge): void
    {
        $this->maxCacheAge = $maxCacheAge;
    }
}
