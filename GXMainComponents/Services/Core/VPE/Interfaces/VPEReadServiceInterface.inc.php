<?php

/* --------------------------------------------------------------
   VPEReadServiceInterface.inc.php 2019-06-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface VPEReadServiceInterface
 *
 * @category   System
 * @package    VPE
 */
interface VPEReadServiceInterface
{
    /**
     * Returns VPE entity by the given id.
     *
     * @param \IdType $vpeId VPE entity to be given.
     *
     * @return \VPEInterface
     * @throws EntityNotFoundException
     */
    public function getById(IdType $vpeId);
    
    
    /**
     * Returns als VPE entities as collection.
     *
     * @return \VPECollection
     */
    public function getAll();
    
    
}
