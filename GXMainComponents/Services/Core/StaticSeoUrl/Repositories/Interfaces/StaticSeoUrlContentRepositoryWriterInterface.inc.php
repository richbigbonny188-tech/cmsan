<?php

/* --------------------------------------------------------------
  StaticSeoUrlContentRepositoryWriterInterface.inc.php 2017-05-24
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2017 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
*/

/**
 * Interface StaticSeoUrlContentRepositoryWriterInterface
 *
 * @category   System
 * @package    StaticSeoUrl
 * @subpackage Interfaces
 */
interface StaticSeoUrlContentRepositoryWriterInterface
{
    /**
     * Inserts a staticSeoUrlContent to the database.
     *
     * @param IdType                       $staticSeoUrlId
     * @param StaticSeoUrlContentInterface $staticSeoUrlContent
     *
     * @return int ID of inserted staticSeoUrlContent or the given staticSeoUrlContent ID if the staticSeoUrlContent
     *             had an ID already.
     */
    public function store(IdType $staticSeoUrlId, StaticSeoUrlContentInterface $staticSeoUrlContent);
}