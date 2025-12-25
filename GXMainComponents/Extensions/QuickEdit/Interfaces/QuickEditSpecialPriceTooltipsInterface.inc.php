<?php

/* --------------------------------------------------------------
   QuickEditSpecialPriceTooltipsInterface.inc.php 2017-03-09
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface QuickEditSpecialPriceTooltipsInterface
 *
 * @category   System
 * @package    Extensions
 * @subpackage QuickEdit
 */
interface QuickEditSpecialPriceTooltipsInterface
{
    /**
     * This method is not currently used. Can be removed.
     *
     * @param QuickEditProductPropertiesListItem $data QuickEdit properties collection
     *
     * @return array Returns an empty array
     */
    public function getRowTooltips(QuickEditProductPropertiesListItem $data);
}