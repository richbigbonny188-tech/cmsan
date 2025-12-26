<?php
/* --------------------------------------------------------------
 ParcelTrackingCodeDeleter.inc.php 2018-01-12
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2018 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Class ParcelTrackingCodeDeleter
 */
class ParcelTrackingCodeDeleter
{
    /**
     * @var \CI_DB_query_builder
     */
    protected $db;
    
    /**
     * @var string
     */
    protected $table = 'orders_parcel_tracking_codes';
    
    
    /**
     * TrackingCodesDeleter constructor.
     *
     * @param \CI_DB_query_builder $db
     */
    public function __construct(CI_DB_query_builder $db)
    {
        $this->db = $db;
    }
    
    
    /**
     * Removes a order parcel tracking code from the database.
     *
     * @param \ParcelTrackingCodeId $id Id of order parcel tracking code entity.
     *
     * @return $this|\ParcelTrackingCodeDeleter Same instance for chained method calls.
     */
    public function delete(ParcelTrackingCodeId $id)
    {
        $this->db->delete($this->table, ['orders_parcel_tracking_code_id' => $id->is()]);
        
        return $this;
    }
}