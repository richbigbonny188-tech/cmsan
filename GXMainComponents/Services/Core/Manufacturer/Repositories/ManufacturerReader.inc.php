<?php
/* --------------------------------------------------------------
   ManufacturerReader.inc.php 2018-07-03
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ManufacturerReader
 *
 * @category   System
 * @package    Manufacturer
 * @subpackage Repositories
 */
class ManufacturerReader extends AbstractDataPaginator implements ManufacturerReaderInterface
{
    
    /**
     * Applies the class default sorting
     */
    protected function _applyDefaultSorting()
    {
        
        $this->db->order_by('manufacturers.manufacturers_id', 'asc')->order_by('manufacturers_info.languages_id',
                                                                               'asc');
    }
    
    
    /**
     * return the child class Field Map array.
     *
     * @return array.
     */
    
    protected function _getFieldMap()
    {
        return [
            'id'           => 'manufacturers.manufacturers_id',
            'name'         => 'manufacturers.manufacturers_name',
            'image'        => 'manufacturers.manufacturers_image',
            'dateadded'    => 'manufacturers.date_added',
            'lastmodified' => 'manufacturers.last_modified'
        ];
    }
    
    
    /**
     * Returns all manufacturer entities data as array.
     *
     * @return array
     */
    public function getAll()
    {
        $rawData = $this->db->select()
            ->from('manufacturers')
            ->join('manufacturers_info',
                   'manufacturers.manufacturers_id = manufacturers_info.manufacturers_id')
            ->order_by('manufacturers.manufacturers_id', 'asc')
            ->order_by('manufacturers_info.languages_id', 'asc')
            ->get()
            ->result_array();
        
        return $this->mapRawData($rawData);
    }
    
    
    /**
     * Searches for manufacturer entities that respects the given search condition and returns their data as an array.
     *
     * @param \ManufacturerSearchCondition $searchCondition
     * @param \Pager|null                  $pager   (Optional) Pager object with pagination information
     * @param array                        $sorters (Optional) array of Sorter objects with data sorting information
     *
     * @return array
     */
    public function search(ManufacturerSearchCondition $searchCondition, \Pager $pager = null, array $sorters = [])
    {
        $this->_applyPagination();
        $this->_applySorting($sorters);
        $this->db->select()
            ->from('manufacturers')
            ->join('manufacturers_info',
                   'manufacturers.manufacturers_id = manufacturers_info.manufacturers_id')
            ->where($searchCondition->buildSql());
        $rawData = $this->db->get()->result_array();
        
        return $this->mapRawData($rawData);
    }
    
    
    /**
     * Returns manufacturer entity data by the given id.
     *
     * @param \IdType $manufacturerId
     *
     * @return array
     * @throws \EntityNotFoundException If no record was found with provided manufacturer entity id.
     */
    public function getById(IdType $manufacturerId)
    {
        $rawData = $this->db->select()
            ->from('manufacturers')
            ->join('manufacturers_info',
                   'manufacturers.manufacturers_id = manufacturers_info.manufacturers_id')
            ->where('manufacturers.manufacturers_id', $manufacturerId->asInt())
            ->get()
            ->result_array() ? : [];
        if (count($rawData) === 0) {
            throw new EntityNotFoundException('Manufacturer entity was not found with provided id "'
                                              . $manufacturerId->asInt() . '"');
        }
        
        $result    = ['id' => $manufacturerId->asInt()];
        $urlsArray = [];
        
        foreach ($rawData as $data) {
            $result['name']                   = $data['manufacturers_name'];
            $result['image']                  = $data['manufacturers_image'];
            $result['dateAdded']              = $data['date_added'];
            $result['lastModified']           = $data['last_modified'];
            $urlsArray[$data['languages_id']] = $data['manufacturers_url'];
        }
        $result['url'] = $urlsArray;
        
        return $result;
    }
    
    
    /**
     * Maps the raw db data into a pattern that can be used in the repository.
     *
     * @param $rawData
     *
     * @return array
     */
    protected function mapRawData($rawData)
    {
        $formattedArray = [];
        $urls           = [];
        
        foreach ($rawData as $data) {
            if (isset($oldId) && $oldId !== (int)$data['manufacturers_id']) {
                $formattedArray[] = $urls;
                $urls             = [];
            }
            $oldId = (int)$data['manufacturers_id'];
            
            $urls['id']                         = $oldId;
            $urls['name']                       = $data['manufacturers_name'];
            $urls['image']                      = $data['manufacturers_image'];
            $urls['dateAdded']                  = $data['date_added'];
            $urls['lastModified']               = $data['last_modified'];
            $urls['url'][$data['languages_id']] = $data['manufacturers_url'];
        }
        if (count($rawData) > 0) {
            $formattedArray[] = $urls;
        }
        
        return $formattedArray;
    }
}