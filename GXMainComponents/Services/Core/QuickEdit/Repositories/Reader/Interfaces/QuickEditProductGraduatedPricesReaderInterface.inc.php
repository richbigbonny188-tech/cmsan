<?php

/* --------------------------------------------------------------
   QuickEditProductGraduatedPricesReaderInterface.inc.php 2017-03-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface QuickEditProductsGraduationsReaderInterface
 *
 * @category   System
 * @package    QuickEdit
 * @subpackage Interfaces
 */
interface QuickEditProductGraduatedPricesReaderInterface
{
    /**
     * Returns the graduated prices of a product or an empty array nothing was found.
     *
     * @param array|null $productIds Array containing the selected product IDs to be processed.
     *
     * @return array Returns array that contains the graduated prices information.
     */
    public function getGraduatedPrices(array $productIds = null);
}