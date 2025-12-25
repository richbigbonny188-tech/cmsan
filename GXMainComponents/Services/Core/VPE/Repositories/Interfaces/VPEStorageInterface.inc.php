<?php

/* --------------------------------------------------------------
   VPEStorageInterface.inc.php 2017-07-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface VPEStorageInterface
 *
 * @category   System
 * @package    VPE
 * @subpackage Repositories
 */
interface VPEStorageInterface
{
    /**
     * Returns VPE entity data by the given id.
     *
     * @param \IdType $vpeId
     *
     * @return array
     */
    public function getById(IdType $vpeId);
    
    
    /**
     * Returns all VPE entities data as array.
     *
     * @return array
     */
    public function getAll();
    
    
    /**
     * Saves VPE entity data in database.
     *
     * @param \VPEInterface $vpe VPE entity to be saved.
     *
     * @return \VPEStorageInterface Same instance for chained method calls.
     */
    public function save(VPEInterface $vpe);
    
    
    /**
     * Deletes VPE entity data in database.
     *
     * @param \VPEInterface $vpe VPE entity to be delete.
     *
     * @return \VPEStorageInterface Same instance for chained method calls.
     */
    public function delete(VPEInterface $vpe);
    
    
    /**
     * Updates VPE entity data in database.
     *
     * @param \VPEInterface $vpe VPE entity to be updated.
     *
     * @return \VPEStorageInterface Same instance for chained method calls.
     */
    public function update(VPEInterface $vpe);
}


