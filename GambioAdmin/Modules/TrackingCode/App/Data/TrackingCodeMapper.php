<?php
/* --------------------------------------------------------------
   TrackingCodeMapper.php 2021-10-08
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\TrackingCode\App\Data;

use Gambio\Admin\Modules\TrackingCode\Model\Collections\TrackingCodeIds;
use Gambio\Admin\Modules\TrackingCode\Model\Collections\TrackingCodes;
use Gambio\Admin\Modules\TrackingCode\Model\TrackingCode;
use Gambio\Admin\Modules\TrackingCode\Model\ValueObjects\OrderId;
use Gambio\Admin\Modules\TrackingCode\Model\ValueObjects\TrackingCodeId;
use Gambio\Admin\Modules\TrackingCode\Services\TrackingCodeFactory;

/**
 * Class TrackingCodeMapper
 *
 * @package Gambio\Admin\Modules\TrackingCode\App\Data
 */
class TrackingCodeMapper
{
    /**
     * @var TrackingCodeFactory
     */
    private $factory;
    
    
    /**
     * TrackingCodeMapper constructor.
     *
     * @param TrackingCodeFactory $factory
     */
    public function __construct(TrackingCodeFactory $factory)
    {
        $this->factory = $factory;
    }
    
    
    /**
     * @param array $data
     *
     * @return TrackingCode
     */
    public function mapTrackingCode(array $data): TrackingCode
    {
        $details = $this->factory->createParcelServiceDetails((int)$data['parcel_service_id'],
                                                              $data['language_code'],
                                                              $data['parcel_service_name'],
                                                              $data['url'],
                                                              $data['comment'],
                                                              $data['shipment_type']);
        
        return $this->factory->createTrackingCode((int)$data['orders_parcel_tracking_code_id'],
                                                  (int)$data['order_id'],
                                                  $data['tracking_code'],
                                                  $details,
                                                  $data['creation_date'],
                                                  $data['is_return_delivery'] === '1');
    }
    
    
    /**
     * @param array $data
     *
     * @return TrackingCodes
     */
    public function mapTrackingCodes(array $data): TrackingCodes
    {
        $trackingCodes = array_map([$this, 'mapTrackingCode'], $data);
        
        return $this->factory->createTrackingCodes(...$trackingCodes);
    }
    
    
    /**
     * @param int $id
     *
     * @return TrackingCodeId
     */
    public function mapTrackingCodeId(int $id): TrackingCodeId
    {
        return $this->factory->createTrackingCodeId($id);
    }
    
    
    /**
     * @param int ...$ids
     *
     * @return TrackingCodeIds
     */
    public function mapTrackingCodeIds(int ...$ids): TrackingCodeIds
    {
        $ids = array_map([$this, 'mapTrackingCodeId'], $ids);
        
        return $this->factory->createTrackingCodeIds(...$ids);
    }
    
    
    /**
     * @param int $id
     *
     * @return OrderId
     */
    public function mapOrderId(int $id): OrderId
    {
        return $this->factory->createOrderId($id);
    }
}