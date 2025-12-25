<?php
/* --------------------------------------------------------------
   GambioHubPayPalController.inc.php 2021-04-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class GambioHubPayPalController extends GambioHubPayPalController_parent
{
    /**
     * returns content for installments options popup
     */
    public function actionInstallmentOptions()
    {
        return parent::actionInstallmentOptions();
    }
}
