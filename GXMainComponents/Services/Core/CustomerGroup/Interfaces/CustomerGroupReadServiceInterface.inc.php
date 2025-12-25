<?php
/* --------------------------------------------------------------
   CustomerGroupReadServiceInterface.inc.php 2017-09-28
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface CustomerGroupReadServiceInterface
 *
 * @category   System
 * @package    CustomerGroup
 * @subpackage Interfaces
 */
interface CustomerGroupReadServiceInterface
{
    /**
     * Returns customer group entities as collection.
     *
     * @return \CustomerGroupCollection
     */
    public function getAll();
    
    
    /**
     * Returns customer group by given id.
     *
     * @param \IntType $id Customer group id.
     *
     * @return \CustomerGroup
     */
    public function getById(IntType $id);
    
    
    /**
     * Creates customer group entity.
     *
     * @return \CustomerGroup new customer group entity.
     */
    public function create();
}