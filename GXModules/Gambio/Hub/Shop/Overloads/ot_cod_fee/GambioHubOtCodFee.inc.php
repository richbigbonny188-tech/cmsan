<?php
/* --------------------------------------------------------------
   GambioHubOtCodFee.inc.php 2022-08-04
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class GambioHubOtCodFee
 */
class GambioHubOtCodFee extends GambioHubOtCodFee_parent
{
    /**
     * @var string
     */
    protected $cashOnDeliveryModuleCode = 'CashOnDeliveryHub';
    
    
    /**
     * Set the $_SESSION['payment'] value temporarily to 'cod' for payment with cash on delivery hub payment module to
     * extend ot_cod_fee functionality for hub payments
     */
    public function process()
    {
        $sessionPaymentBackup = $_SESSION['payment'] ?? null;
        $gambioHubSelection   = $_SESSION['gambio_hub_selection'] ?? null;
        
        if ($sessionPaymentBackup === 'gambio_hub'
            && $gambioHubSelection === $this->cashOnDeliveryModuleCode) {
            $_SESSION['payment'] = 'cod';
        }
        
        parent::process();
        
        $_SESSION['payment'] = $sessionPaymentBackup;
    }
}
