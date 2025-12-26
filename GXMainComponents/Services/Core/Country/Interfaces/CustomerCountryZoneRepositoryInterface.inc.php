<?php
/* --------------------------------------------------------------
   CustomerCountryZoneRepositoryInterface.inc.php 2017-03-20 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface CustomerCountryZoneRepositoryInterface
 *
 * @category   System
 * @package    Customer
 * @subpackage Interfaces
 */
interface CustomerCountryZoneRepositoryInterface
{
    
    /**
     * Method to get a country zone with a given ID
     *
     * @param IdType $countryZoneId
     *
     * @return CustomerCountryZoneInterface
     * @throws Exception if country zone not found
     *
     */
    public function getById(IdType $countryZoneId);
    
    
    /**
     * Method to get a county zone with a given name and country
     *
     * @param CustomerCountryZoneNameInterface $countryZoneName
     * @param CustomerCountryInterface         $country
     *
     * @return CustomerCountryZoneInterface
     * @throws Exception if country zone not found
     *
     */
    public function getByNameAndCountry(
        CustomerCountryZoneNameInterface $countryZoneName,
        CustomerCountryInterface $country
    );
    
    
    /**
     * This method will get the country zone by its name and country if it exists, if not it will return null.
     *
     * @param CustomerCountryZoneNameInterface $countryZoneName
     * @param CustomerCountryInterface         $country
     *
     * @return CustomerCountryZone|null
     */
    public function findByNameAndCountry(
        CustomerCountryZoneNameInterface $countryZoneName,
        CustomerCountryInterface $country
    );
    
    
    /**
     * This method will get all country zones by a country ID if it exists, if not it will return an empty array.
     *
     * @param IdType $countryId
     *
     * @return array
     */
    public function findCountryZonesByCountryId(IdType $countryId);
    
    
    /**
     * Method to get a country zone by ID if exists else return null
     *
     * @param IdType $countryZoneId
     *
     * @return CustomerCountryZone|null
     */
    public function findById(IdType $countryZoneId);
    
    
    /**
     * This method will return a new CustomerCountryZoneName object representing an unknown country zone.
     * ID is 0 and ISO code is empty.
     *
     * @param CustomerCountryZoneNameInterface $countryZoneName
     *
     * @return CustomerCountryZone
     */
    public function getUnknownCountryZoneByName(CustomerCountryZoneNameInterface $countryZoneName);
    
    
    /**
     * This method returns whether the specified country, necessary, needs a state.
     *
     * @param IdType $countryId
     *
     * @return bool
     */
    public function isStateMandatory(IdType $countryId);
    
    
    /**
     * Finds the country zone by the given zone code and country.
     * Null will be returned if no results are found.
     *
     * @param \CustomerCountryZoneIsoCodeInterface $zoneCode Zone code of expected country zone.
     * @param \CustomerCountryInterface            $country  Country of zone.
     *
     * @return \CustomerCountryZoneInterface|null Country zone model.
     */
    public function findCountryZoneByZoneCodeAndCountry(
        CustomerCountryZoneIsoCodeInterface $zoneCode,
        CustomerCountryInterface $country
    );
    
    
    /**
     * Returns the country zone by the given zone code and country id.
     * Null will be returned if no results are found.
     *
     * @param \CustomerCountryZoneIsoCodeInterface $zoneCode  Zone code of expected country zone.
     * @param \IdType                              $countryId Country id of zone.
     *
     * @return \CustomerCountryZoneInterface Country zone model.
     */
    public function findCountryZoneByZoneCodeAndCountryId(
        CustomerCountryZoneIsoCodeInterface $zoneCode,
        IdType $countryId
    );
}