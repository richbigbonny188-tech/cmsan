<?php
/* --------------------------------------------------------------
   CustomerReaderInterface.inc.php 2018-07-02
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface CustomerReaderInterface
 *
 * @category   System
 * @package    Customer
 * @subpackage Interfaces
 */
interface CustomerReaderInterface
{
    /**
     * Finds a customer by the given ID.
     *
     * @param IdType $id Customer's ID.
     *
     * @return Customer|null Customer or null if not found.
     */
    public function findById(IdType $id);
    
    
    /**
     * Finds a registree by email address.
     *
     * @param CustomerEmailInterface $email Customer's E-Mail address.
     *
     * @return Customer|null Customer or null if not found.
     */
    public function findRegistreeByEmail(CustomerEmailInterface $email);
    
    
    /**
     * Finds a guest by email address.
     *
     * @param CustomerEmailInterface $email Customer's E-Mail address.
     *
     * @return Customer|null Customer or null if not found.
     */
    public function findGuestByEmail(CustomerEmailInterface $email);
    
    
    /**
     * Filters customer records and returns the total count.
     *
     * Example:
     *        $reader->filterCustomers( array('customers_id' => 1) );
     *
     * @param array $conditions Associative array containing the desired field and value.
     *
     * @return int Returns the total customers count.
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