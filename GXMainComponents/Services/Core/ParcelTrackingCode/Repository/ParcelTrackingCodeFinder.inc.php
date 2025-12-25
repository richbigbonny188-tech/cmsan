<?php
/* --------------------------------------------------------------
 ParcelTrackingCodeFinder.inc.php 2018-01-15
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2018 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Class ParcelTrackingCodeFinder
 */
class ParcelTrackingCodeFinder extends AbstractDataPaginator
{
    
    /**
     * Applies the class default sorting
     */
    protected function _applyDefaultSorting()
    {
        
    }
    
    
    /**
     * return the child class Field Map array.
     *
     * @return array.
     */
    
    protected function _getFieldMap()
    {
        return [
            'id'                => $this->table . '.orders_parcel_tracking_code_id',
            'orderid'           => $this->table . '.order_id',
            'trackingcode'      => $this->table . '.tracking_code',
            'parcelserviceid'   => $this->table . '.parcel_service_id',
            'parcelservicename' => $this->table . '.parcel_service_name',
            'languageid'        => $this->table . '.language_id',
            'url'               => $this->table . '.url',
            'comment'           => $this->table . '.comment',
            'creationdate'      => $this->table . '.creation_date '
        ];
    }
    
    
    /**
     * @var string
     */
    protected $table = 'orders_parcel_tracking_codes';
    
    
    /**
     * Returns parcel tracking code data by the given tracking code id.
     *
     * @param \ParcelTrackingCodeId $id Id of searched parcel tracking code entry.
     *
     * @return array
     */
    public function find(ParcelTrackingCodeId $id)
    {
        return $this->db->where('orders_parcel_tracking_code_id', $id->is())->get($this->table)->row_array() ? : [];
    }
    
    
    /**
     * Returns parcel tracking codes data by the given order id.
     *
     * @param \ParcelTrackingCodeOrderId $orderId Id of order.
     *
     * @return array
     */
    public function findByOrderId(ParcelTrackingCodeOrderId $orderId)
    {
        return $this->db->where('order_id', $orderId->is())->get($this->table)->result_array() ? : [];
    }
    
    
    /**
     * Returns all parcel tracking code data.
     *
     * @param \Pager|null $pager   (Optional) Pager object with pagination information
     * @param array       $sorters (Optional) array of Sorter objects with data sorting information
     *
     * @return array
     */
    public function getAll(\Pager $pager = null, array $sorters = [])
    {
        $this->_applyPagination($pager)->_applySorting($sorters);
        
        return $this->db->get($this->table)->result_array() ? : [];
    }
}