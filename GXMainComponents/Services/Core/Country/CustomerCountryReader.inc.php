<?php
/* --------------------------------------------------------------
   CustomerCountryReader.inc.php 2022-02-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('CustomerCountryReaderInterface');

/**
 * Class CustomerCountryReader
 *
 * This class is used for reading customer country data from the database
 *
 * @category   System
 * @package    Customer
 * @subpackage Country
 * @implements CustomerCountryReaderInterface
 */
class CustomerCountryReader implements CustomerCountryReaderInterface
{
    /**
     * @var AbstractCustomerFactory
     */
    protected $customerFactory;
    
    /**
     * @var CI_DB_query_builder
     */
    protected $db;
    
    
    /**
     * Constructor of the class CustomerCountryReader
     *
     * @param AbstractCustomerFactory $customerFactory
     * @param CI_DB_query_builder     $dbQueryBuilder
     */
    public function __construct(AbstractCustomerFactory $customerFactory, CI_DB_query_builder $dbQueryBuilder)
    {
        $this->customerFactory = $customerFactory;
        $this->db              = $dbQueryBuilder;
    }
    
    
    /**
     * @return CustomerCountryCollection
     */
    public function getAll()
    {
        $countriesDataArray = $this->db->get('countries')->result_array();
        
        $listItems  = array_map([$this, '_getCountryByArray'], $countriesDataArray);
        $collection = MainFactory::create(CustomerCountryCollection::class, $listItems);
        
        return $collection;
    }
    
    
    /**
     * @param IdType $countryId
     *
     * @return CustomerCountry|null
     */
    public function findById(IdType $countryId)
    {
        $countryDataArray = $this->db->get_where('countries', ['countries_id' => $countryId->asInt()])->row_array();
        if (empty($countryDataArray)) {
            return null;
        }
        
        return $this->_getCountryByArray($countryDataArray);
    }
    
    
    /**
     * @param $countryName
     *
     * @return CustomerCountry|null
     */
    public function findByName(CustomerCountryNameInterface $countryName)
    {
        $countryDataArray = $this->db->get_where('countries', ['countries_name' => (string)$countryName])->row_array();
        
        if (empty($countryDataArray)) {
            return null;
        }
        
        return $this->_getCountryByArray($countryDataArray);
    }
    
    
    /**
     * @param $countryIso2
     *
     * @return CustomerCountry|null
     */
    public function findByIso2(CustomerCountryIso2Interface $countryIso2)
    {
        $countryDataArray = $this->db->get_where('countries', ['countries_iso_code_2' => (string)$countryIso2])
            ->row_array();
        
        if (empty($countryDataArray)) {
            return null;
        }
        
        return $this->_getCountryByArray($countryDataArray);
    }
    
    
    /**
     * This method returns whether the specified country, necessary, needs a state.
     *
     * @param IdType $countryId
     *
     * @return bool
     */
    public function isStateMandatory(IdType $countryId)
    {
        $stateMandatory = $this->db->get_where('countries', ['countries_id' => $countryId->asInt()])->row_array();
        
        return (bool)$stateMandatory['is_state_mandatory'];
    }
    
    
    /**
     * @param $countryDataArray
     *
     * @return CustomerCountry
     */
    protected function _getCountryByArray($countryDataArray)
    {
        $country = $this->customerFactory->createCustomerCountry(new IdType((int)$countryDataArray['countries_id']),
                                                                 MainFactory::create('CustomerCountryName',
                                                                                     $countryDataArray['countries_name']),
                                                                 MainFactory::create('CustomerCountryIso2',
                                                                                     $countryDataArray['countries_iso_code_2']),
                                                                 MainFactory::create('CustomerCountryIso3',
                                                                                     $countryDataArray['countries_iso_code_3']),
                                                                 new IdType((int)$countryDataArray['address_format_id']),
                                                                 (boolean)(int)$countryDataArray['status']);
        
        return $country;
    }
} 