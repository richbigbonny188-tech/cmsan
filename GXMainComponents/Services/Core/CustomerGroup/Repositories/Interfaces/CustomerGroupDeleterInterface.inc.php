<?php
/* --------------------------------------------------------------
   CustomerGroupDeleterInterface.inc.php 2018-02-01
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface CustomerGroupDeleterInterface
 *
 * @category   System
 * @package    CustomerGroup
 * @subpackage Repositories
 */
interface CustomerGroupDeleterInterface
{
    /**
     * Deletes customer group entity data in database with personal offer table.
     *
     * @param \CustomerGroup $customerGroup Customer group entity to be delete.
     *
     * @return $this|\CustomerGroupDeleterInterface Same instance for chained method calls.
     */
    public function delete(CustomerGroup $customerGroup);
    
}