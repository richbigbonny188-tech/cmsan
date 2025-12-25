<?php

/* --------------------------------------------------------------
  StaticSeoUrlRepositoryReaderInterface.inc.php 2017-05-24
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2017 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
*/

/**
 * Interface StaticSeoUrlRepositoryReaderInterface
 *
 * @category   System
 * @package    StaticSeoUrl
 * @subpackage Interfaces
 */
interface StaticSeoUrlRepositoryReaderInterface
{
    /**
     * Returns a StaticSeoUrl instance by the given staticSeoUrl ID.
     *
     * @param IdType $staticSeoUrlId
     *
     * @return StaticSeoUrlInterface
     */
    public function getById(IdType $staticSeoUrlId);
    
    
    /**
     * Returns a StaticSeoUrlCollection with all existing StaticSeoUrl objects.
     *
     * @return StaticSeoUrlCollection
     */
    public function getAll();
}