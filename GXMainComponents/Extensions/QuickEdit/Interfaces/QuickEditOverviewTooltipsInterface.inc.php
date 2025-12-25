<?php

/* --------------------------------------------------------------
   QuickEditOverviewTooltipsInterface.inc.php 2017-03-09
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface QuickEditOverviewTooltipsInterface
 *
 * @category   System
 * @package    Extensions
 * @subpackage QuickEdit
 */
interface QuickEditOverviewTooltipsInterface
{
    /**
     * Returns the data of a product for the tooltip.
     *
     * @param QuickEditProductListItem $data QuickEdit product collection.
     *
     * @return array Returns the data of a product for the tooltip.
     */
    public function getRowTooltips(QuickEditProductListItem $data);
}