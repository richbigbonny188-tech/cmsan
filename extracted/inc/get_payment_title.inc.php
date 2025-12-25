<?php
/* --------------------------------------------------------------
   get_payment_title.inc.php 2015-07-30 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * @param string $p_methodName name of the payment class
 * @param string $p_languageDirectoryName
 * @param bool   $p_stripTags  remove HTML-tags like images
 *
 * @throws InvalidArgumentException if $p_methodName is not a string or is empty
 * @throws InvalidArgumentException if $p_languageDirectoryName is not a string
 *
 * @return string name of the payment method
 */
function get_payment_title($p_methodName, $p_languageDirectoryName = '', $p_stripTags = true)
{
	if($p_stripTags)
	{
		return PaymentTitleProvider::getStrippedTagsTitle($p_methodName, $p_languageDirectoryName);
	}
	
	return PaymentTitleProvider::getTitle($p_methodName, $p_languageDirectoryName);
}