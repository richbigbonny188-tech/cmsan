<?php
/* --------------------------------------------------------------
   GambioHubCheckoutConfirmationContentControl.inc.php 2023-07-17
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class GambioHubCheckoutConfirmationContentControl
 */
class GambioHubCheckoutConfirmationContentControl extends GambioHubCheckoutConfirmationContentControl_parent
{
    /**
     * Maximum age of a Gambio Hub session in seconds
     */
    const SESSION_MAX_AGE = 3300;

    /**
     * @param $error
     *
     * @return boolean
     */
    protected function _checkTransportConditions(&$error)
    {
    	// set gambio hub module selection before redirecting to checkout payment page in error cases
	    if(isset($_POST) && array_key_exists('gambio_hub_selection', $_POST))
	    {
		    $_SESSION['gambio_hub_selection'] = $_POST['gambio_hub_selection'];
	    }
    	
        if(array_key_exists('shipping', $_SESSION) && is_array($_SESSION['shipping'])
           && array_key_exists('id', $_SESSION['shipping']) && !is_array(explode(',', gm_get_conf('DATA_TRANSFER_TO_TRANSPORT_COMPANIES_SETTINGS')))
        )
        {
            return true;
        }
        
        return parent::_checkTransportConditions($error);
    }
    
    
    /**
     * Causes redirect back to payment selection in case Hub session is too old.
     *
     * @return bool
     */
    public function check_payment()
    {
        $paymentAllowed = parent::check_payment();
    
        if (isset($this->v_data_array['POST']['payment'])
            && preg_match('/gambio_hub-(?<hubselection>[\w]+)(-(?<hubsubselection>[\w]+))?/',
                $this->v_data_array['POST']['payment'], $matches) === 1) {
            $this->v_data_array['POST']['payment'] = 'gambio_hub';
            $_POST['payment'] = 'gambio_hub';
            $this->v_data_array['POST']['gambio_hub_selection'] = $matches['hubselection'];
            $_POST['gambio_hub_selection'] = $matches['hubselection'];
            if (!empty($matches['hubsubselection'])) {
                $this->v_data_array['POST']['gambio_hub_subselection'] = $matches['hubsubselection'];
                $_POST['gambio_hub_subselection'] = $matches['hubsubselection'];
            } else {
                $this->v_data_array['POST']['gambio_hub_subselection'] = '';
                $_POST['gambio_hub_subselection'] = '';
            }
        }
        
        if (($_SESSION['payment'] ?? null) === 'gambio_hub') {
            $sessionTooOld = empty($_SESSION['gambio_hub_session_key_refreshed'])
                             || (microtime(true) - $_SESSION['gambio_hub_session_key_refreshed'])
                                > self::SESSION_MAX_AGE;
            if ($sessionTooOld) {
                $paymentAllowed = false;
            }
        }
        
        return $paymentAllowed;
    }
}
