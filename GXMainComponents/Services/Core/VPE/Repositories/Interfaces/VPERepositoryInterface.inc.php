<?php

/* --------------------------------------------------------------
   VPERepositoryInterface.inc.php 2019-06-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface VPERepositoryInterface
 *
 * @category   System
 * @package    VPE
 * @subpackage Repositories
 */
interface VPERepositoryInterface
{
    /**
     * Returns VPE entity by the given id.
     *
     * @param \IdType $vpeId IdType of entity to be returned.
     *
     * @return \VPEInterface
     * @throws EntityNotFoundException
     */
    public function getById(IdType $vpeId);
    
    /**
     * Returns if a VPE entity exists
     *
     * @param \IdType $vpeId IdType of entity to be returned.
     *
     * @return \VPEInterface
     */
    
    /**
     * Returns all VPE entities as collection.
     *
     * @return \VPECollection VPE collection.
     */
    public function getAll();
    
    
    /**
     * Saves VPE entity in database.
     *
     * @param \VPEInterface $vpe Vpe entity to be save.
     *
     * @return \VPERepositoryInterface Same instance for chained method calls.
     */
    public function save(VPEInterface $vpe);
    
    
    /**
     * Deletes VPE entity from database.
     *
     * @param \VPEInterface $vpe VPE entity to be deleted.
     *
     * @return \VPERepositoryInterface Same instance for chained method calls.
     */
    public function delete(VPEInterface $vpe);
    
    
    /**
     * Creates VPE entity.
     *
     * @return \VPE New VPE entity.
     */
    public function createVPE();
}