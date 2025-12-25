<?php
/* --------------------------------------------------------------
   CustomerGroupReader.inc.php 2018-01-30
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class CustomerGroupReader
 *
 * @category   System
 * @package    CustomerGroup
 * @subpackage Repositories
 */
class CustomerGroupReader implements CustomerGroupReaderInterface
{
    /**
     * @var \CI_DB_query_builder
     */
    protected $queryBuilder;
    
    
    public function __construct(CI_DB_query_builder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }
    
    
    /**
     * Returns all customer group entities data as array.
     *
     * @return array
     */
    public function getAll()
    {
        $rawData    = $this->_getAllGroups();
        $configData = $this->_getDefaultValue();
        
        $resultData     = [];
        $customerGroups = [];
        
        foreach ($rawData as $data) {
            if (isset($oldId) && $oldId !== (int)$data['customers_status_id']) {
                $resultData[]   = $customerGroups;
                $customerGroups = [];
            }
            $oldId = (int)$data['customers_status_id'];
            
            $customerGroups['id']                          = $oldId;
            $customerGroups['names'][$data['language_id']] = $data['customers_status_name'];
            $customerGroups['default']                     = $configData[0]['value']
                                                             === (string)$customerGroups['id'];
            $customerGroups['members']                     = $this->_countMembers($oldId);
            $customerGroups['settings']                    = $this->_getSettingsData($data);
            $customerGroups['configurations']              = $this->_getConfigurationsData($data);
        }
        if (count($rawData) > 0) {
            $resultData[] = $customerGroups;
        }
        
        return $resultData;
    }
    
    
    /**
     * Returns customer group entity data by the given id.
     *
     * @param \IntType $id
     *
     * @return array
     * @throws \EntityNotFoundException
     *
     */
    public function getById(IntType $id)
    {
        $rawData = $this->queryBuilder->select()
            ->from('customers_status')
            ->where('customers_status_id', $id->asInt())
            ->order_by('customers_status_id', 'asc')
            ->order_by('language_id', 'asc')
            ->get()
            ->result_array() ? : [];
        
        $configData = $this->_getDefaultValue();
        
        if (count($rawData) === 0) {
            throw new EntityNotFoundException('Customer group entity was not found with provided id "' . $id->asInt()
                                              . '"');
        }
        
        $result                   = ['id' => $id->asInt()];
        $result['default']        = $configData[0]['value'] === (string)$id->asInt();
        $result['settings']       = $this->_getSettingsData($rawData[0]);
        $result['configurations'] = $this->_getConfigurationsData($rawData[0]);
        $result['members']        = $this->_countMembers($id->asInt());
        
        $customerGroupsNames = [];
        
        foreach ($rawData as $data) {
            $customerGroupsNames[$data['language_id']] = $data['customers_status_name'];
        }
        $result['names'] = $customerGroupsNames;
        
        return $result;
    }
    
    
    /**
     * Returns customer group counted members by id.
     *
     * @param $int
     *
     * @return int
     */
    protected function _countMembers($int)
    {
        $number = $this->queryBuilder->query('SELECT COUNT(`customers_status`) AS `total`
											  FROM `customers`
											  WHERE `customers_status`=' . $int)->result_array();
        
        return count($number) ? $number[0]['total'] : 0;
    }
    
    
    /**
     * Gets the default value from configuration table.protected
     *
     * @return array
     */
    protected function _getDefaultValue()
    {
        return $this->queryBuilder->select('value')
            ->from('gx_configurations')
            ->where('key',
                    'configuration/DEFAULT_CUSTOMERS_STATUS_ID')
            ->get()
            ->result_array();
    }
    
    
    /**
     * Gets the Settings data from data array.
     *
     * @param array $customerGroupData
     *
     * @return array
     */
    protected function _getSettingsData(array $customerGroupData)
    {
        return [
            'public'             => (int)$customerGroupData['customers_status_public'] === 1,
            'otDiscount'         => (int)$customerGroupData['customers_status_ot_discount_flag'] === 1,
            'graduatedPrices'    => (int)$customerGroupData['customers_status_graduated_prices'] === 1,
            'showPrice'          => (int)$customerGroupData['customers_status_show_price'] === 1,
            'showPriceTax'       => (int)$customerGroupData['customers_status_show_price_tax'] === 1,
            'addTaxOt'           => (int)$customerGroupData['customers_status_add_tax_ot'] === 1,
            'discountAttributes' => (int)$customerGroupData['customers_status_discount_attributes'] === 1,
            'fsk18Purchasable'   => (int)$customerGroupData['customers_fsk18_purchasable'] === 1,
            'fsk18Display'       => (int)$customerGroupData['customers_fsk18_display'] === 1,
            'writeReviews'       => (int)$customerGroupData['customers_status_write_reviews'] === 1,
            'readReviews'        => (int)$customerGroupData['customers_status_read_reviews'] === 1,
        ];
    }
    
    
    /**
     * Gets the Configuration data from data array.
     *
     * @param array $customerGroupData
     *
     * @return array
     */
    protected function _getConfigurationsData(array $customerGroupData)
    {
        return [
            'minOrder'                 => $customerGroupData['customers_status_min_order'] ? (double)$customerGroupData['customers_status_min_order'] : null,
            'maxOrder'                 => $customerGroupData['customers_status_max_order'] ? (double)$customerGroupData['customers_status_max_order'] : null,
            'discount'                 => $customerGroupData['customers_status_discount'] ? (double)$customerGroupData['customers_status_discount'] : null,
            'otDiscount'               => $customerGroupData['customers_status_ot_discount'] ? (double)$customerGroupData['customers_status_ot_discount'] : null,
            'unallowedPaymentModules'  => $customerGroupData['customers_status_payment_unallowed'] !== '' ? explode(',',
                                                                                                                    $customerGroupData['customers_status_payment_unallowed']) : [],
            'unallowedShippingModules' => $customerGroupData['customers_status_shipping_unallowed']
                                          !== '' ? explode(',',
                                                           $customerGroupData['customers_status_shipping_unallowed']) : [],
        ];
    }
    
    
    /**
     * Returns an array of all customer groups from customer_status table.
     *
     * @return array
     */
    protected function _getAllGroups()
    {
        return $this->queryBuilder->from('customers_status')
            ->order_by('customers_status_id', 'asc')
            ->order_by('language_id',
                       'asc')
            ->get()
            ->result_array() ? : [];
    }
}