<?php
/* --------------------------------------------------------------
   CustomerCountryReader.inc.php 2021-11-26 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('CustomerCountryZoneReaderInterface');

/**
 * Class CustomerCountryZoneReader
 *
 * This class is used for reading customer country zone data from the database
 *
 * @category   System
 * @package    Customer
 * @subpackage CountryZone
 * @implements CustomerCountryZoneReaderInterface
 */
class CustomerCountryZoneReader implements CustomerCountryZoneReaderInterface
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
     * Constructor of the class CustomerCountryZoneReader
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
     * @param CustomerCountryZoneNameInterface $countryZoneName
     *
     * @return CustomerCountryZone
     */
    public function findByName(CustomerCountryZoneNameInterface $countryZoneName)
    {
        $zoneDataArray = $this->db->get_where('zones', ['zone_name' => (string)$countryZoneName])->row_array();
        if (empty($zoneDataArray)) {
            return null;
        }
        
        return $this->_createCountryZoneByArray($zoneDataArray);
    }
    
    
    /**
     * @param CustomerCountryZoneNameInterface $countryZoneName
     * @param CustomerCountryInterface         $country
     *
     * @return CustomerCountryZone|null
     */
    public function findByNameAndCountry(
        CustomerCountryZoneNameInterface $countryZoneName,
        CustomerCountryInterface $country
    ) {
        $zoneDataArray = $this->db->get_where('zones',
                                              [
                                                  'zone_name'       => (string)$countryZoneName,
                                                  'zone_country_id' => $country->getId()
                                              ])->row_array();
        
        if (empty($zoneDataArray)) {
            return null;
        }
        
        return $this->_createCountryZoneByArray($zoneDataArray);
    }
    
    
    /**
     * @param IdType $countryZoneId
     *
     * @return CustomerCountryZone
     */
    public function findById(IdType $countryZoneId)
    {
        $zoneDataArray = [];
        $zoneId        = $countryZoneId->asInt();
        if ($zoneId !== 0) {
            $zoneDataArray = $this->db->get_where('zones', ['zone_id' => $zoneId])->row_array();
            if (empty($zoneDataArray)) {
                return null;
            }
        }
        
        return $this->_createCountryZoneByArray($zoneDataArray);
    }
    
    
    /**
     * @param IdType $countryId
     *
     * @return array of CustomerCountryZone objects
     */
    public function findCountryZonesByCountryId(IdType $countryId)
    {
        $zonesArray = $this->db->get_where('zones', ['zone_country_id' => $countryId->asInt()])->result_array();
        foreach ($zonesArray as &$zone) {
            $zone = $this->_createCountryZoneByArray($zone);
        }
        
        return $zonesArray;
    }
    
    
    /**
     * Returns an array with country zone data.
     * An empty array will be returned if no results are found.
     *
     * @param \CustomerCountryZoneIsoCodeInterface $zoneCode Zone code of expected country zone.
     * @param \CustomerCountryInterface            $country  Country of zone.
     *
     * @return \CustomerCountryZoneInterface|null Country zone.
     */
    public function findCountryZoneByZoneCodeAndCountry(
        CustomerCountryZoneIsoCodeInterface $zoneCode,
        CustomerCountryInterface $country
    ) {
        $zoneData = $this->_getZoneDataByZoneCodeAndCountryId((string)$zoneCode, $country->getId());
        if (!$zoneData) {
            return null;
        }
        
        return $this->_createCountryZoneByArray($zoneData);
    }
    
    
    /**
     * Returns an array with country zone data.
     * An empty array will be returned if no results are found.
     *
     * @param \CustomerCountryZoneIsoCodeInterface $zoneCode  Zone code of expected country zone.
     * @param \IdType                              $countryId Country id of zone.
     *
     * @return \CustomerCountryZoneInterface|null Country zone.
     */
    public function findCountryZoneByZoneCodeAndCountryId(
        CustomerCountryZoneIsoCodeInterface $zoneCode,
        IdType $countryId
    ) {
        $zoneData = $this->_getZoneDataByZoneCodeAndCountryId((string)$zoneCode, $countryId->asInt());
        
        if (!$zoneData) {
            return null;
        }
        
        return $this->_createCountryZoneByArray($zoneData);
    }
    
    
    /**
     * Creates a customer country zone object from the given zone data.
     *
     * @param array $zoneDataArray Zone data, must contain keys "zone_id", "zone_name" and "zone_code".
     *
     * @return CustomerCountryZone
     */
    protected function _createCountryZoneByArray(array $zoneDataArray)
    {
        $countryZone = $this->customerFactory->createCustomerCountryZone(new IdType((int)$zoneDataArray['zone_id']),
                                                                         MainFactory::create('CustomerCountryZoneName',
                                                                             (string)$zoneDataArray['zone_name']),
                                                                         MainFactory::create('CustomerCountryZoneIsoCode',
                                                                                             (string)$zoneDataArray['zone_code']));
        
        return $countryZone;
    }
    
    
    /**
     * Fetches data for a zone by the given zone code and country id.
     *
     * @param string $zoneCode  ISO code of expected zone.
     * @param int    $countryId Id of zone's country.
     *
     * @return array Data for a zone.
     */
    protected function _getZoneDataByZoneCodeAndCountryId($zoneCode, $countryId)
    {
        return $this->db->select()
            ->from('zones')
            ->where('zone_country_id', $countryId)
            ->where('zone_code', $zoneCode)
            ->get()
            ->row_array();
    }
}