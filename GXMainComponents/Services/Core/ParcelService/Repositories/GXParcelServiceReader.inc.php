<?php
/* --------------------------------------------------------------
   ParcelServiceReader.inc.php 2018-07-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ParcelServiceReader
 */
class GXParcelServiceReader extends AbstractDataPaginator implements ParcelServiceReaderInterface
{
    
    
    /**
     * Maps the entity Fields with the database fields
     *
     * @var $fieldMap
     */
    public static $fieldMap = [
        'id'        => 'parcel_services.parcel_service_id',
        'name'      => 'parcel_services.name',
        'isdefault' => 'parcel_services.default'
    ];
    
    
    /**
     * Returns all parcel services limited by given limit and offset.
     *
     * @param \Pager|null $pager   (Optional) Pager object with pagination information
     * @param array       $sorters (Optional) array of Sorter objects with data sorting information
     *
     * @return array
     */
    public function fetchAll(\Pager $pager = null, array $sorters = [])
    {
        $this->db->select()->from('parcel_services');
        $this->_applyPagination($pager);
        $this->_applySorting($sorters);
        
        $parcelServicesData = $this->db->get()->result_array();
        
        return $this->_prepareParcelServicesData($parcelServicesData);
    }
    
    
    /**
     * Returns the parcel service search result limited by given limit and offset as an array.
     *
     * @param \StringType $searchCondition
     * @param \Pager|null $pager   (Optional) Pager object with pagination information
     * @param array       $sorters (Optional) array of Sorter objects with data sorting information
     *
     * @return array
     */
    public function fetchBy(\StringType $searchCondition, \Pager $pager = null, array $sorters = [])
    {
        $this->db->select()->from('parcel_services')->where($searchCondition->asString());
        $this->_applyPagination($pager);
        $this->_applySorting($sorters);
        
        $parcelServicesData = $this->db->get()->result_array();
        
        return $this->_prepareParcelServicesData($parcelServicesData);
    }
    
    
    /**
     * Returns a parcel service as an array identified by its ID.
     *
     * @param \ParcelServiceId $parcelServiceId
     *
     * @return array
     */
    public function fetchById(\ParcelServiceId $parcelServiceId)
    {
        $rawData = $this->db->select()
            ->from('parcel_services')
            ->join('parcel_services_description',
                   'parcel_services.parcel_service_id = parcel_services_description.parcel_service_id')
            ->where('parcel_services.parcel_service_id', $parcelServiceId->id())
            ->get()
            ->result_array();
        
        return $this->_prepareParcelData($rawData);
    }
    
    
    /**
     * Applies the class default sorting
     */
    protected function _applyDefaultSorting()
    {
        $this->db->order_by('parcel_services.parcel_service_id', 'asc');
    }
    
    
    /**
     * return the child class Field Map array.
     *
     * @return array.
     */
    
    protected function &_getFieldMap()
    {
        return self::$fieldMap;
    }
    
    
    protected function _prepareParcelServicesData(array $parcelServicesData)
    {
        foreach ($parcelServicesData as &$parcelServiceData) {
            $parcelServiceData['descriptions'] = $this->db->select()
                ->from('parcel_services_description')
                ->where('parcel_service_id',
                        $parcelServiceData['parcel_service_id'])
                ->get()
                ->result_array();
        }
        
        return $parcelServicesData;
    }
    
    
    protected function _prepareParcelData(array $parcelData)
    {
        $parcelServiceData = [];
        $descriptions      = [];
        
        foreach ($parcelData as $parcelDataSet) {
            $parcelServiceData = [
                'parcel_service_id' => $parcelDataSet['parcel_service_id'],
                'name'              => $parcelDataSet['name'],
                'default'           => $parcelDataSet['default']
            ];
            
            $descriptions[] = [
                'parcel_service_id' => $parcelDataSet['parcel_service_id'],
                'language_id'       => $parcelDataSet['language_id'],
                'url'               => $parcelDataSet['url'],
                'comment'           => $parcelDataSet['comment']
            ];
        }
        
        if (!count($parcelServiceData)) {
            return [];
        }
        
        return array_merge($parcelServiceData,
                           [
                               'descriptions' => $descriptions
                           ]);
    }
}