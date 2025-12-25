<?php
/* --------------------------------------------------------------
   CustomerRepositoryInterface.php 2018-07-02
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface CustomerRepositoryInterface
 *
 * @category   System
 * @package    Customer
 * @subpackage Interfaces
 */
interface CustomerRepositoryInterface
{
    /**
     * Creates a new customer.
     *
     * @return Customer Newly created customer.
     */
    public function getNewCustomer();
    
    
    /**
     * Stores customer data in the database.
     *
     * @param CustomerInterface $customer Customer.
     */
    public function store(CustomerInterface $customer);
    
    
    /**
     * Finds a registered customer based on the e-mail address.
     *
     * @param CustomerEmailInterface $email Customer's E-Mail address.
     *
     * @return Customer|null Customer or null if not found.
     */
    public function getRegistreeByEmail(CustomerEmailInterface $email);
    
    
    /**
     * Deletes a guest account by its email address.
     *
     * @param CustomerEmailInterface $email Guest customer's E-Mail address.
     */
    public function deleteGuestByEmail(CustomerEmailInterface $email);
    
    
    /**
     * Returns a guest account by its email address.
     *
     * @param CustomerEmailInterface $email Guest customer's E-Mail address.
     *
     * @return Customer|null Customer or null if not found.
     */
    public function getGuestByEmail(CustomerEmailInterface $email);
    
    
    /**
     * Filters customer records and returns the total count.
     *
     * Example:
     *        $repository->filterCustomers('customers_id' => 1);
     *
     * @param array $conditions Associative array containing the desired field and value.
     *
     * @return int
     */
    public function getFilterCustomersCount(array $conditions = []);
    
    
    /**
     * Filters customer records and returns an array with results.
     *
     * Example:
     *        $repository->filterCustomers('customers_id' => 1);
     *
     * @param array       $conditions Associative array containing the desired field and value.
     * @param \Pager|null $pager      (Optional) Pager object with pagination information
     * @param array       $sorters    (Optional) array of Sorter objects with data sorting information
     *
     * @return array Returns an array that contains customer objects.
     */
    public function filterCustomers(array $conditions = [], \Pager $pager = null, array $sorters = []);
    
    
    /**
     * Filters customer records by a given CustomerSearchCondition object and returns an array with results.
     *
     * @param \CustomerSearchCondition $condition Conditions object for search.
     * @param \Pager|null              $pager     (Optional) Pager object with pagination information
     * @param array                    $sorters   (Optional) array of Sorter objects with data sorting information
     *
     * @return array Returns an array that contains customer objects.
     */
    public function searchCustomers(CustomerSearchCondition $condition, \Pager $pager = null, array $sorters = []);
}