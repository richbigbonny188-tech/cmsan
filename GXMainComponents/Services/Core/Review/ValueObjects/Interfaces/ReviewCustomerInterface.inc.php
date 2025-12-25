<?php
/* --------------------------------------------------------------
   ReviewCustomerInterface.inc.php 2018-01-10
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface ReviewCustomerInterface
 *
 * @category   System
 * @package    Review
 * @subpackage ValueObjects
 */
interface ReviewCustomerInterface
{
    /**
     * Returns the customer id.
     *
     * @return int The customer id.
     */
    public function getCustomerId();
    
    
    /**
     * Returns the customer name.
     *
     * @return string The customer name.
     */
    public function getCustomerName();
}
