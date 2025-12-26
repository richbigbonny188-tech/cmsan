<?php
/* --------------------------------------------------------------
   PersonalDataServiceFactoryInterface.inc.php 2018-05-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface PersonalDataServiceFactoryInterface
 */
interface PersonalDataServiceFactoryInterface
{
    /**
     * Creates a PersonalDataService instance.
     *
     * @return PersonalDataServiceInterface
     */
    public function createService();
}