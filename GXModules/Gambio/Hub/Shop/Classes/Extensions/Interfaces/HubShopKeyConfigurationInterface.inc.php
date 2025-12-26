<?php

/* --------------------------------------------------------------
   HubShopKeyConfigurationInterface.inc.php 2016-12-23
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface HubShopKeyConfigurationInterface
 *
 * @category   System
 * @package    Extensions
 * @subpackage GambioHub
 */
interface HubShopKeyConfigurationInterface
{
	/**
	 * Returns the shop key from the database.
	 *
	 * @return string Returns the shop key.
	 *
	 * @throws RuntimeException If no shop key exists.
	 */
	public function get();
}