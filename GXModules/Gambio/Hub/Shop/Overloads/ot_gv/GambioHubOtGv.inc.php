<?php
/* --------------------------------------------------------------
   GambioHubOtGv.inc.php 2017-10-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class GambioHubOtGv extends GambioHubOtGv_parent
{
	public function __construct()
	{
		parent::__construct();
		$_SESSION['cot_gv'] = isset($_SESSION['cot_gv']) ? (bool)$_SESSION['cot_gv'] : true;
	}

    /**
     * Suppresses output of checkmark on checkout_payment.
     *
     * @return string
     */
    public function use_credit_amount()
    {
        return '';
    }
}
