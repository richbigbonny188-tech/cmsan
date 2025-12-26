<?php

/* --------------------------------------------------------------
   QuickEditProductSpecialPricesWriterInterface.inc.php 2017-03-08
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface QuickEditProductSpecialPricesWriterInterface
 *
 * @category   System
 * @package    QuickEdit
 * @subpackage Interfaces
 */
interface QuickEditProductSpecialPricesWriterInterface
{
    /**
     * Saves the changed data regarding the special price.
     *
     * @param array $specialPrice Contains the special prices data.
     *
     * @return bool Returns the operation result.
     */
    public function setSpecialPriceById(array $specialPrice);
}