<?php

/* --------------------------------------------------------------
  StaticSeoUrlRepositoryInterface.inc.php 2017-05-24
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2017 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
*/

/**
 * Interface StaticSeoUrlRepositoryInterface
 *
 * @category   System
 * @package    StaticSeoUrl
 * @subpackage Interfaces
 */
interface StaticSeoUrlRepositoryInterface
{
    /**
     * Returns a StaticSeoUrlCollection with all existing StaticSeoUrl objects.
     *
     * @return StaticSeoUrlCollection
     */
    public function getAll();
    
    
    /**
     * Returns a StaticSeoUrl instance by the given staticSeoUrl ID.
     *
     * @param IdType $staticSeoUrlId
     *
     * @return StaticSeoUrlInterface
     */
    public function getById(IdType $staticSeoUrlId);
    
    
    /**
     * Stores a StaticSeoUrl to the database.
     *
     * @param StaticSeoUrlInterface $staticSeoUrl
     *
     * @return StaticSeoUrlRepositoryInterface Same instance for method chaining.
     */
    public function store(StaticSeoUrlInterface $staticSeoUrl);
    
    
    /**
     * Deletes a StaticSeoUrl by the given staticSeoUrl ID.
     *
     * @param IdType $staticSeoUrlId
     *
     * @return StaticSeoUrlRepositoryInterface Same instance for method chaining.
     */
    public function deleteStaticSeoUrlById(IdType $staticSeoUrlId);
}