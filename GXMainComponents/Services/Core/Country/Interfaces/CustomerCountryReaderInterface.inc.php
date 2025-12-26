<?php
/* --------------------------------------------------------------
   CustomerCountryReaderInterface.inc.php 2022-02-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface CustomerCountryReaderInterface
 *
 * @category   System
 * @package    Customer
 * @subpackage Interfaces
 */
interface CustomerCountryReaderInterface
{
    /**
     * Method to get all countries
     *
     * @return CustomerCountryCollection
     */
    public function getAll();
    
    
    /**
     * Method to find a country with a given ID if it exists else it will return null
     *
     * @param IdType $countryId
     *
     * @return CustomerCountry
     * @throws Exception if country not found
     * @throws InvalidArgumentException if $p_countryId is not a valid ID
     */
    public function findById(IdType $countryId);
    
    
    /**
     * Method to find a country with a given name if it exists else it will return null
     *
     * @param $countryName
     *
     * @return CustomerCountry|null
     */
    public function findByName(CustomerCountryNameInterface $countryName);
    
    
    /**
     * Method to find a country with a given iso2 code if it exists else it will return null
     *
     * @param $countryIso2
     *
     * @return CustomerCountry|null
     */
    public function findByIso2(CustomerCountryIso2Interface $countryIso2);
    
    
    /**
     * This method returns whether the specified country, necessary, needs a state.
     *
     * @param IdType $countryId
     *
     * @return bool
     */
    public function isStateMandatory(IdType $countryId);
    
}