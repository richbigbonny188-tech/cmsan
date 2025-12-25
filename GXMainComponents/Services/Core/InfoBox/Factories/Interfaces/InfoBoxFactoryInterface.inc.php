<?php

/* --------------------------------------------------------------
   InfoBoxFactoryInterface.inc.php 2016-08-25
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface InfoBoxFactoryInterface
 *
 * @category   System
 * @package    InfoBox
 * @subpackage Interfaces
 */
interface InfoBoxFactoryInterface
{
    /**
     * Creates an InfoBoxService instance.
     *
     * @return InfoBoxService
     */
    public function createInfoBoxService();
}