<?php
/* --------------------------------------------------------------
   KlarnaHubPrice.php 2018-02-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class KlarnaHubPrice
 *
 * Handles KlarnaHub price operations.
 * 
 * @package    GXModules
 * @subpackage GambioHub
 */
class KlarnaHubPrice
{
	/**
	 * Returns sanitized KlarnaHub prices.
	 *
	 * Sometimes the conversion of float values to integer might cause changes in the original amount.
	 *
	 * Example:
	 *
	 * (int)((float)(9.7) * 100) --> 969
	 *
	 * This class handles such effects by first rounding the float value and then converting it to an integer.
	 *
	 * @param $price
	 *
	 * @return int Returns the sanitized value.
	 */
	public static function sanitize($price)
	{
		return (int)round($price);
	}
}