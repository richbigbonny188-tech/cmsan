<?php
/* --------------------------------------------------------------
   AddressBookServiceInterface.inc.php 2015-02-06 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface AddressBookServiceInterface
 *
 * @category   System
 * @package    Customer
 * @subpackage Interfaces
 */
interface AddressBookServiceInterface
{
    /**
     * Method to add a new address in the address book
     *
     * @param AddressBlockInterface $addressBlock
     * @param CustomerInterface     $customer
     *
     * @return CustomerAddressInterface
     */
    public function createNewAddress(AddressBlockInterface $addressBlock, CustomerInterface $customer);
    
    
    /**
     * Method to update an address in the address book
     *
     * @param AddressBlockInterface    $addressBlock
     * @param CustomerAddressInterface $address
     *
     * @return CustomerAddressInterface
     */
    public function updateAddress(AddressBlockInterface $addressBlock, CustomerAddressInterface $address);
    
    
    /**
     * Method to delete an address from the address book
     *
     * @param CustomerAddressInterface $address
     */
    public function deleteAddress(CustomerAddressInterface $address);
    
    
    /**
     * Get all registered addresses.
     *
     * @param \Pager|null $pager   (Optional) Pager object with pagination information
     * @param array       $sorters (Optional) array of Sorter objects with data sorting information
     *
     * @return array Returns an array of CustomerAddress objects.
     */
    
    public function getAllAddresses(\Pager $pager = null, array $sorters = []);
    
    
    /**
     * Filter registered addresses by string.
     *
     * @param string      $p_keyword Used to filter the address records.
     * @param \Pager|null $pager     (Optional) Pager object with pagination information
     * @param array       $sorters   (Optional) array of Sorter objects with data sorting information
     *
     * @return array Returns an array of CustomerAddress objects.
     */
    public function filterAddresses($p_keyword, \Pager $pager = null, array $sorters = []);
    
    
}