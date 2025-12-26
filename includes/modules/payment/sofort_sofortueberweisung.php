<?php
/* --------------------------------------------------------------
   sofort_sofortueberweisung.php 2021-05-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * @version SOFORT Gateway 5.2.0 - $Date: 2013-04-22 14:00:13 +0200 (Mon, 22 Apr 2013) $
 * @author  SOFORT AG (integration@sofort.com)
 * @link    http://www.sofort.com/
 *
 * Copyright (c) 2012 SOFORT AG
 *
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
 *
 * $Id: sofort_sofortueberweisung.php 6097 2013-04-22 12:00:13Z rotsch $
 */

require_once(DIR_FS_CATALOG . 'callback/sofort/sofort.php');
require_once(DIR_FS_CATALOG . 'callback/sofort/library/sofortLib.php');

class sofort_sofortueberweisung_ORIGIN extends sofort
{
    
    public function __construct()
    {
        global $order;
        
        parent::__construct();
        
        $this->_checkExistingSofortConstants('su');
        $this->code          = 'sofort_sofortueberweisung';
        $this->title         = MODULE_PAYMENT_SOFORT_SOFORTUEBERWEISUNG_TEXT_TITLE_ADMIN;
        $this->title_extern  = defined('MODULE_PAYMENT_SOFORT_SU_TEXT_TITLE') ? MODULE_PAYMENT_SOFORT_SU_TEXT_TITLE : '';
        $this->paymentMethod = 'SU';
        
        if (defined('MODULE_PAYMENT_SOFORT_SU_KS_STATUS') && MODULE_PAYMENT_SOFORT_SU_KS_STATUS == 'True') {
            xtc_db_query('UPDATE `gx_configurations` SET `value` = \'false\' where `key` = \'configuration/MODULE_PAYMENT_SOFORT_SU_KS_STATUS\'');
        }
        
        if (defined('MODULE_PAYMENT_SOFORT_SU_RECOMMENDED_PAYMENT')
            && MODULE_PAYMENT_SOFORT_SU_RECOMMENDED_PAYMENT == 'True') {
            $this->title_extern .= ' ' . MODULE_PAYMENT_SOFORT_SU_RECOMMENDED_PAYMENT_TEXT;
        }
    
        $this->enabled = defined('MODULE_PAYMENT_SOFORT_SU_STATUS')
                         && filter_var(MODULE_PAYMENT_SOFORT_SU_STATUS, FILTER_VALIDATE_BOOLEAN);
        
        $this->description = MODULE_PAYMENT_SOFORT_SU_TEXT_DESCRIPTION . '<br />'
                             . MODULE_PAYMENT_SOFORT_MULTIPAY_VERSIONNUMBER . ': '
                             . HelperFunctions::getSofortmodulVersion();
        
        if ($this->_isInstalled() && !$this->_modulVersionCheck()) {
            $this->description = '<span style ="color:red; font-weight: bold; font-size: 1.2em">'
                                 . MODULE_PAYMENT_SOFORT_MULTIPAY_UPDATE_NOTICE . '</span><br /><br />'
                                 . $this->description;
        }
        
        $this->description .= MODULE_PAYMENT_SOFORT_SU_TEXT_DESCRIPTION_EXTRA;
        $this->sort_order  = (defined('MODULE_PAYMENT_SOFORT_SU_SORT_ORDER') ? MODULE_PAYMENT_SOFORT_SU_SORT_ORDER : false);
        
        if (is_object($order)) {
            $this->update_status();
        }
        
        if (defined('MODULE_PAYMENT_SOFORT_SU_STATUS')) {
            $this->sofort = new SofortLib_Multipay(MODULE_PAYMENT_SOFORT_MULTIPAY_APIKEY);
            $this->sofort->setVersion(HelperFunctions::getSofortmodulVersion());
            
            if (defined('MODULE_PAYMENT_SOFORT_MULTIPAY_LOG_ENABLED')
                && MODULE_PAYMENT_SOFORT_MULTIPAY_LOG_ENABLED == "True") {
                $this->sofort->setLogEnabled();
            }
        }
    }
    
    
    function selection()
    {
        if (!parent::selection()) {
            $this->sofort->log("Notice: Paymentmethod " . $this->code . " will be deactivated for selection.");
            $this->enabled = false;
            
            return false;
        }
        
        $logoUrl = 'https://cdn.klarna.com/1.0/shared/image/generic/badge/:lang:/pay_now/standard/pink.svg';
        
        if (in_array($_SESSION['language_code'], ['de', 'pl', 'fr', 'nl', 'it', 'es'])) {
            $lang = $_SESSION['language_code'] . '_' . $_SESSION['language_code'];
        } else {
            $lang = 'en_gb';
        }
        $logoUrl = str_replace(':lang:', $lang, $logoUrl);
        
        if (MODULE_PAYMENT_SOFORT_MULTIPAY_IMAGE === 'Logo & Text') {
            $text = MODULE_PAYMENT_SOFORT_MULTIPAY_SU_CHECKOUT_TEXT;
        }
        
        $title = $this->_setImageText('', $text);
        
        //add ks-link, if ks is active
        $title = str_replace('[[link_beginn]]',
                             '<a href="' . MODULE_PAYMENT_SOFORT_MULTIPAY_SU_CHECKOUT_INFOLINK_KS
                             . '" target="_blank" style="cursor: pointer; text-decoration: underline;">',
                             $title);
        $title = str_replace('[[link_end]]', '</a>', $title);
        
        $cost = '';
        if (array_key_exists('ot_sofort', $GLOBALS)) {
            $cost = $GLOBALS['ot_sofort']->get_percent($this->code, 'price');
        }
        
        $selection = [
            'id'          => $this->code,
            'module'      => $this->title_extern,
            'description' => $title,
            'module_cost' => $cost,
            'logo_url'    => $logoUrl,
            'logo_alt'    => MODULE_PAYMENT_SOFORT_SU_TEXT_DESCRIPTION_CHECKOUT_PAYMENT_IMAGEALT,
        ];
        
        return $selection;
    }
    
    
    function _setImageText($image, $text)
    {
        if (!empty($image)) {
            $image = xtc_image($image,
                               MODULE_PAYMENT_SOFORT_SU_TEXT_DESCRIPTION_CHECKOUT_PAYMENT_IMAGEALT,
                               '',
                               '',
                               'style="float: right; margin: 1em 1ex;"');
        }
        $title = MODULE_PAYMENT_SOFORT_SU_TEXT_DESCRIPTION_CHECKOUT_PAYMENT_IMAGE;
        $title = str_replace('{{image}}', $image, $title);
        $title = str_replace('{{text}}', $text, $title);
        
        return $title;
    }
    
    
    function install()
    {
        $sofortStatuses  = $this->_insertAndReturnSofortStatus();
        $checkStatus     = (isset($sofortStatuses['check'])
                            && !empty($sofortStatuses['check'])) ? $sofortStatuses['check'] : '';
        $refundedStatus  = (isset($sofortStatuses['refunded'])
                            && !empty($sofortStatuses['refunded'])) ? $sofortStatuses['refunded'] : '';
        $confirmedStatus = (isset($sofortStatuses['translate_confirmed'])
                            && !empty($sofortStatuses['translate_confirmed'])) ? $sofortStatuses['translate_confirmed'] : '';
        $unchangedStatus = (isset($sofortStatuses['unchanged'])
                            && !empty($sofortStatuses['unchanged'])) ? $sofortStatuses['unchanged'] : '';
        
        xtc_db_query("INSERT INTO `gx_configurations` (`key`, `value`, `sort_order`, `type`, `last_modified`) VALUES ('configuration/MODULE_PAYMENT_SOFORT_SU_STATUS', 'False', '1', 'switcher', now())");
        xtc_db_query("INSERT INTO `gx_configurations` (`key`, `value`, `sort_order`, `type`, `last_modified`) VALUES ('configuration/MODULE_PAYMENT_SOFORT_SU_KS_STATUS', 'False', '30', 'switcher', now())");
        xtc_db_query("INSERT INTO `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) VALUES ('configuration/MODULE_PAYMENT_SOFORT_SOFORTUEBERWEISUNG_ALLOWED', '', '12', now())");
        xtc_db_query("INSERT INTO `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) VALUES ('configuration/MODULE_PAYMENT_SOFORT_SU_SORT_ORDER', '0', '16', now())");
        xtc_db_query("INSERT INTO `gx_configurations` (`key`, `value`, `sort_order`, `type`, `last_modified`) VALUES ('configuration/MODULE_PAYMENT_SOFORT_SU_RECOMMENDED_PAYMENT', 'False', '5', 'switcher', now())");
        xtc_db_query("INSERT INTO `gx_configurations` (`key`, `value`, `sort_order`, `type`, `last_modified`) VALUES ('configuration/MODULE_PAYMENT_SOFORT_SU_ZONE', '0', '13', 'geo-zone', now())");
        
        //"Best�tigt": pending-not_credited_yet
        //Important notice: constantname is also used for status: untraceable-sofort_bank_account_needed
        xtc_db_query("INSERT INTO `gx_configurations` (`key`, `value`, `sort_order`, `type`, `last_modified`) VALUES ('configuration/MODULE_PAYMENT_SOFORT_SU_PEN_NOT_CRE_YET_STATUS_ID', '"
                     . HelperFunctions::escapeSql($confirmedStatus) . "', '30', 'order-status', now())");
        
        //"Bestellung pr�fen": loss-not_credited
        xtc_db_query("INSERT INTO `gx_configurations` (`key`, `value`, `sort_order`, `type`, `last_modified`) VALUES ('configuration/MODULE_PAYMENT_SOFORT_SU_LOS_NOT_CRE_STATUS_ID', '"
                     . HelperFunctions::escapeSql($checkStatus) . "', '30', 'order-status', now())");
        
        //"Geldeingang": received-credited
        xtc_db_query("INSERT INTO `gx_configurations` (`key`, `value`, `sort_order`, `type`, `last_modified`) VALUES ('configuration/MODULE_PAYMENT_SOFORT_SU_REC_CRE_STATUS_ID', '"
                     . HelperFunctions::escapeSql($unchangedStatus) . "', '30', 'order-status', now())");
        
        //"Teilr�ckbuchung": refunded-compensation
        xtc_db_query("INSERT INTO `gx_configurations` (`key`, `value`, `sort_order`, `type`, `last_modified`) VALUES ('configuration/MODULE_PAYMENT_SOFORT_SU_REF_COM_STATUS_ID', '"
                     . HelperFunctions::escapeSql($unchangedStatus) . "', '30', 'order-status', now())");
        
        //"Vollst�ndige R�ckbuchung": refunded-refunded
        xtc_db_query("INSERT INTO `gx_configurations` (`key`, `value`, `sort_order`, `type`, `last_modified`) VALUES ('configuration/MODULE_PAYMENT_SOFORT_SU_REF_REF_STATUS_ID', '"
                     . HelperFunctions::escapeSql($refundedStatus) . "', '30', 'order-status', now())");
        
        //install shared keys, that are used by all/most multipay-modules
        parent::install();
    }
    
    
    function remove()
    {
        xtc_db_query("DELETE FROM `gx_configurations` where `key` LIKE 'configuration/MODULE_PAYMENT_SOFORT_SU%'");
        xtc_db_query("DELETE FROM `gx_configurations` where `key` LIKE 'configuration/MODULE_PAYMENT_SOFORT_SOFORTUEBERWEISUNG%'");
        
        //if this is the last removing of a multipay-paymentmethod --> we also remove all shared keys, that are used by all/most multipay-modules
        parent::remove();
    }
    
    
    function keys()
    {
        parent::keys();
        
        return [
            'configuration/MODULE_PAYMENT_SOFORT_SU_STATUS',
            'configuration/MODULE_PAYMENT_SOFORT_MULTIPAY_APIKEY',
            'configuration/MODULE_PAYMENT_SOFORT_MULTIPAY_AUTH',
            'configuration/MODULE_PAYMENT_SOFORT_SU_RECOMMENDED_PAYMENT',
            'configuration/MODULE_PAYMENT_SOFORT_MULTIPAY_IMAGE',
            'configuration/MODULE_PAYMENT_SOFORT_MULTIPAY_REASON_1',
            'configuration/MODULE_PAYMENT_SOFORT_MULTIPAY_REASON_2',
            'configuration/MODULE_PAYMENT_SOFORT_SOFORTUEBERWEISUNG_ALLOWED',
            'configuration/MODULE_PAYMENT_SOFORT_SU_ZONE',
            'configuration/MODULE_PAYMENT_SOFORT_SU_SORT_ORDER',
            'configuration/MODULE_PAYMENT_SOFORT_MULTIPAY_PROF_SETTINGS',
            'configuration/MODULE_PAYMENT_SOFORT_MULTIPAY_TEMP_STATUS_ID',
            'configuration/MODULE_PAYMENT_SOFORT_MULTIPAY_ABORTED_STATUS_ID',
            'configuration/MODULE_PAYMENT_SOFORT_SU_PEN_NOT_CRE_YET_STATUS_ID',
            'configuration/MODULE_PAYMENT_SOFORT_SU_LOS_NOT_CRE_STATUS_ID',
            'configuration/MODULE_PAYMENT_SOFORT_SU_REC_CRE_STATUS_ID',
            'configuration/MODULE_PAYMENT_SOFORT_SU_REF_COM_STATUS_ID',
            'configuration/MODULE_PAYMENT_SOFORT_SU_REF_REF_STATUS_ID',
            //'MODULE_PAYMENT_SOFORT_SU_CHECK_STATUS_ID',
            //'MODULE_PAYMENT_SOFORT_SU_KS_STATUS',
            //'MODULE_PAYMENT_SOFORT_MULTIPAY_LOG_ENABLED',
        ];
    }


    function check()
    {
        if (!isset ($this->_check)) {
            $check_query  = xtc_db_query("SELECT `value` from `gx_configurations` where `key` = 'configuration/MODULE_PAYMENT_SOFORT_SU_STATUS'");
            $this->_check = xtc_db_num_rows($check_query);
        }

        return $this->_check;
    }
}

MainFactory::load_origin_class('sofort_sofortueberweisung');
