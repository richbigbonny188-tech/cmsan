<?php

/* --------------------------------------------------------------
   InfoBoxRepositoryReaderInterface.php 2016-08-26
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface InfoBoxReaderInterface
 *
 * @category   System
 * @package    InfoBox
 * @subpackage Interfaces
 */
interface InfoBoxRepositoryReaderInterface
{
    /**
     * Returns all messages.
     *
     * @return InfoBoxMessageCollection
     */
    public function getAll();
}
