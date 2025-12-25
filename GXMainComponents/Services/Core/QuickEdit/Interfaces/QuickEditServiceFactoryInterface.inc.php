<?php

/* --------------------------------------------------------------
   QuickEditServiceFactoryInterface.inc.php 2017-03-09
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface QuickEditServiceFactoryInterface
 *
 * @category   System
 * @package    QuickEdit
 * @subpackage Interfaces
 */
interface QuickEditServiceFactoryInterface
{
    /**
     * Creates and returns a new quickEdit service instance.
     *
     * @return QuickEditServiceInterface Returns QuickEdit service.
     */
    public function createQuickEditService();
}