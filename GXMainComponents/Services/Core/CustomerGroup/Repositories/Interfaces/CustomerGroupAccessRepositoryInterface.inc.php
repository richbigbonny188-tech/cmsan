<?php
/* --------------------------------------------------------------
   CustomerGroupAccessRepositoryInterface.inc.php 2017-09-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface CustomerGroupAccessRepositoryInterface
 *
 * @category   System
 * @package    CustomerGroup
 * @subpackage Repositories
 */
interface CustomerGroupAccessRepositoryInterface
{
    /**
     * Returns all customer group as collection.
     *
     * @return \CustomerGroupCollection Customer group collection.
     */
    public function getAll();
    
    
    /**
     * Returns customer group entity by given id.
     *
     * @param \IntType $id IdType of entity to be returned.
     *
     * @return \CustomerGroup
     */
    public function getById(IntType $id);
    
    
    /**
     * creates customer group entity.
     *
     * @return \CustomerGroup New customer group entity.
     */
    public function create();
    
}