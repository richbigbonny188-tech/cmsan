<?php
/* --------------------------------------------------------------
	PayPalThirdPartyPaymentsHelper.inc.php 2020-06-08
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2018 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

/**
 * Utility class for the generation of the PayPal Plus third party payments configuration during checkout
 */
class PayPalThirdPartyPaymentsHelper
{
    /**
     * returns JSON-encoded configuration of third party payments.
     * @return string JSON block containing third party payments configuration
     * @deprecated This feature is no longer supported. (2020-06-08)
     */
    public function getThirdPartyPaymentsBlock(): string
    {
        return json_encode([]);
    }
}
