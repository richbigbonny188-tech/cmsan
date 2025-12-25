<?php
/* --------------------------------------------------------------
	ParcelShopFinder.inc.php 2022-08-29
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2022 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

use GuzzleHttp\Client;

/**
 * Class ParcelShopFinder
 *
 * “ParcelShopFinder” is actually a misnomer, now. This class implements access to DHL’s LocationFinder API, which
 * is the successor to ParcelShopFinder.
 *
 * @category   System
 * @package    Extensions
 * @subpackage ParcelShopFinder
 */
class ParcelShopFinder
{
    /**
     * maximum distance of entities returned (in meters)
     */
    protected const MAX_DISTANCE = 15000;
    
    /**
     * @var ParcelShopFinderLogger
     */
    protected $logger;
    
    
    /**
     * ParcelShopFinder constructor.
     */
    public function __construct()
    {
        $this->logger = MainFactory::create('ParcelShopFinderLogger');
    }
    
    
    public function findLocationsByAddress(
        LocationFinderAddress $address,
        int                   $radius = 15000,
        int                   $limit = 50,
        bool                  $servicePoints = true,
        bool                  $postOffices = true,
        bool                  $lockers = true
    ): array {
        $radius = (int)max($radius, self::MAX_DISTANCE);
    
        // $url = "https://api-sandbox.dhl.com/location-finder/v1/find-by-address";
        $url        = "https://api.dhl.com/location-finder/v1/find-by-address";
        $apiKey     = '2oZzhRwnsBtBfeJRGllf99eDGnvt5Qrh';
        
        $query                 = $address->asArray();
        $query['providerType'] = 'parcel';
        $query['radius']       = $radius;
        $query['limit']        = $limit;
        $encodedQuery          = http_build_query($query, '', '&');
        $encodedQuery          .= $servicePoints ? '&locationType=servicepoint' : '';
        $encodedQuery          .= $lockers ? '&locationType=locker' : '';
        $encodedQuery          .= $postOffices ? '&locationType=postoffice' : '';
        //$encodedQuery .= '&locationType=postbank';
        
        $client   = new Client();
        $response = $client->get($url . '?' . $encodedQuery,
                                 [
                                     'headers' => [
                                         'DHL-Api-Key' => $apiKey,
                                         'Accept'      => 'application/json',
                                     ],
                                 ]);
        
        if ($response->getStatusCode() === 200) {
            $responseData = json_decode($response->getBody()->getContents(), true);
            
            return $responseData['locations'] ?? [];
        }
        
        $this->logger->noticeDebug("Got error response ({$response->getStatusCode()}):\n{$response->getBody()->getContents()}");
        
        return [];
    }
}
