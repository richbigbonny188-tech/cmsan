<?php
/* --------------------------------------------------------------
   SpecialOfferReader.inc.php 2018-07-02
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class SpecialOfferReader extends AbstractDataPaginator implements SpecialOfferReaderInterface
{
    
    /**
     * @var string
     */
    protected $table = 'specials';
    
    
    /**
     * Applies the class default sorting
     */
    protected function _applyDefaultSorting()
    {
        $this->db->order_by($this->table . '.specials_id', 'asc');
    }
    
    
    /**
     * return the child class Field Map array.
     *
     * @return array.
     */
    
    protected function _getFieldMap()
    {
        return [
            'id'        => $this->table . '.specials_id',
            'price'     => $this->table . '.specials_new_products_price',
            'quantity'  => $this->table . '.specials_quantity',
            'status'    => $this->table . '.status',
            'beginsat'  => $this->table . '.begins_date',
            'expiresat' => $this->table . '.expires_date',
            'productid' => $this->table . '.products_id',
            'added'     => $this->table . '.specials_date_added',
            'modified'  => $this->table . '.specials_last_modified'
        ];
    }
    
    
    /**
     * Fetches special offer data by the given id.
     *
     * @param \IdType $specialOfferId Id of expected special offer data.
     *
     * @return array|null Raw data of a special offer.
     */
    public function fetchById(IdType $specialOfferId)
    {
        return $this->db->select()
            ->from($this->table)
            ->where('specials_id', $specialOfferId->asInt())
            ->get()
            ->row_array();
    }
    
    
    /**
     * Fetches special offer data by the given condition.
     *
     * @param \StringType $searchCondition Search condition for special offer data.
     * @param \Pager|null $pager           (Optional) Pager object with pagination information
     * @param array       $sorters         (Optional) array of Sorter objects with data sorting information
     *
     * @return array|null Raw data of special offers.
     */
    public function fetchBy(StringType $searchCondition, \Pager $pager = null, array $sorters = [])
    {
        $this->_applyPagination($pager);
        $this->_applySorting($sorters);
        
        return $this->db->select()
            ->from($this->table)
            ->where($searchCondition->asString(), null, false)
            ->get()
            ->result_array();
    }
    
    
    /**
     * Returns all special offers.
     *
     * @param \Pager|null $pager   (Optional) Pager object with pagination information
     * @param array       $sorters (Optional) array of Sorter objects with data sorting information
     *
     * @return array|null Raw data of special offers.
     */
    public function fetchAll(\Pager $pager = null, array $sorters = [])
    {
        $this->_applyPagination($pager);
        $this->_applySorting($sorters);
        
        return $this->db->select()->from($this->table)->get()->result_array();
    }
}