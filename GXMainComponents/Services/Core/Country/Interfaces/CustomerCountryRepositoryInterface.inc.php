<?php
/* --------------------------------------------------------------
   CustomerCountryRepositoryInterface.inc.php 2022-02-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface CustomerCountryRepositoryInterface
 *
 * @category   System
 * @package    Customer
 * @subpackage Interfaces
 */
interface CustomerCountryRepositoryInterface
{
    /**
     * Method to get all customer countries
     *
     * @return CustomerCountryCollection
     */
    public function getAll();
    
    
    /**
     * Method to get a customer country with a given country ID
     *
     * @param IdType $countryId
     *
     * @return CustomerCountryInterface
     */
    public function getById(IdType $countryId);
    
    
    /**
     * Method to find a country if exists else return null
     *
     * @param IdType $countryId
     *
     * @return CustomerCountry|null
     */
    public function findById(IdType $countryId);
    
    
    /**
     * Get country by name.
     *
     * @param CustomerCountryNameInterface $countryName
     *
     * @return CustomerCountry
     *
     * @throws Exception If the country could not be found.
     */
    public function getByName(CustomerCountryNameInterface $countryName);
    
    
    /**
     * Find country by name.
     *
     * @param CustomerCountryNameInterface $countryName
     *
     * @return CustomerCountry
     */
    public function findByName(CustomerCountryNameInterface $countryName);
    
    
    /**
     * Get country by iso2 code.
     *
     * @param CustomerCountryIso2Interface $countryIso2
     *
     * @return CustomerCountry
     *
     * @throws Exception If the country could not be found.
     */
    public function getByIso2(CustomerCountryIso2Interface $countryIso2);
    
    
    /**
     * Find country by iso2 code.
     *
     * @param CustomerCountryIso2Interface $countryIso2
     *
     * @return CustomerCountry|null
     */
    public function findByIso2(CustomerCountryIso2Interface $countryIso2);
}