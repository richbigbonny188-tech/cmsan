<?php

/* --------------------------------------------------------------
  StaticSeoUrlContentRepositoryInterface.inc.php 2017-05-24
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2017 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
*/

/**
 * Interface StaticSeoUrlContentRepositoryInterface
 *
 * @category   System
 * @package    StaticSeoUrl
 * @subpackage Interfaces
 */
interface StaticSeoUrlContentRepositoryInterface
{
    /**
     * Returns a StaticSeoUrlContentCollection instance by the given staticSeoUrl ID.
     *
     * @param IdType $staticSeoUrlId
     *
     * @return StaticSeoUrlContentCollection
     */
    public function getByStaticSeoUrlId(IdType $staticSeoUrlId);
    
    
    /**
     * Returns a StaticSeoUrlContent instance by the given staticSeoUrlContent ID.
     *
     * @param IdType $staticSeoUrlContentId
     *
     * @return StaticSeoUrlContentInterface
     */
    public function getById(IdType $staticSeoUrlContentId);
    
    
    /**
     * Stores a StaticSeoUrlContent to the database.
     *
     * @param IdType                       $staticSeoUrlId
     * @param StaticSeoUrlContentInterface $staticSeoUrlContent
     *
     * @return StaticSeoUrlContentRepositoryInterface Same instance for method chaining.
     */
    public function store(IdType $staticSeoUrlId, StaticSeoUrlContentInterface $staticSeoUrlContent);
    
    
    /**
     * Deletes a StaticSeoUrlContent by the given staticSeoUrlContent ID.
     *
     * @param IdType $staticSeoUrlContentId
     *
     * @return StaticSeoUrlContentRepositoryInterface Same instance for method chaining.
     */
    public function deleteStaticSeoUrlContentById(IdType $staticSeoUrlContentId);
}