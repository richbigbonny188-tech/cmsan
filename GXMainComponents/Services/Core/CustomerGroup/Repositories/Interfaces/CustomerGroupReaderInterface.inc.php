<?php
/* --------------------------------------------------------------
   CustomerGroupReaderInterface.inc.php 2017-09-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface CustomerGroupReaderInterface
 *
 * @category   System
 * @package    CustomerGroup
 * @subpackage Repositories
 */
interface CustomerGroupReaderInterface
{
    /**
     * Returns all customer group entities data as array.
     *
     * @return array
     */
    public function getAll();
    
    
    /**
     * Returns customer group entity data by the given id.
     *
     * @param \IntType $id
     *
     * @return array
     */
    public function getById(IntType $id);
}