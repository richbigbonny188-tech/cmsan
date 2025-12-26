<?php

/* --------------------------------------------------------------
   StaticSeoUrlWriteServiceInterface.inc.php 2017-05-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface StaticSeoUrlWriteServiceInterface
 *
 * @category   System
 * @package    StaticSeoUrl
 * @subpackage Interfaces
 */
Interface StaticSeoUrlWriteServiceInterface
{
    /**
     * Deletes a StaticSeoUrl by the given static seo url ID.
     *
     * @param IdType $staticSeoUrlId
     *
     * @return StaticSeoUrlWriteServiceInterface Same instance for method chaining.
     */
    public function deleteStaticSeoUrlById(IdType $staticSeoUrlId);
    
    
    /**
     * Saves a static seo url to the database and delegates to child-repositories.
     *
     * @param StaticSeoUrlInterface $staticSeoUrl
     *
     * @return StaticSeoUrlInterface The StaticSeoUrl instance.
     */
    public function saveStaticSeoUrl(StaticSeoUrlInterface $staticSeoUrl);
}