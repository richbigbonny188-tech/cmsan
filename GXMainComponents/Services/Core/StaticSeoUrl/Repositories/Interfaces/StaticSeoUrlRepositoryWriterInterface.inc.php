<?php

/* --------------------------------------------------------------
  StaticSeoUrlRepositoryWriterInterface.inc.php 2017-05-24
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2017 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
*/

/**
 * Interface StaticSeoUrlRepositoryWriterInterface
 *
 * @category   System
 * @package    StaticSeoUrl
 * @subpackage Interfaces
 */
interface StaticSeoUrlRepositoryWriterInterface
{
    /**
     * Inserts a staticSeoUrl to the database.
     *
     * @param StaticSeoUrlInterface $staticSeoUrl
     *
     * @return int ID of inserted staticSeoUrl or the given staticSeoUrl ID if the staticSeoUrl had an ID already.
     */
    public function store(StaticSeoUrlInterface $staticSeoUrl);
}