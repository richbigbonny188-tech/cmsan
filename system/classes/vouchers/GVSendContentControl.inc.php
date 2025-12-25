<?php
/* --------------------------------------------------------------
   GVSendContentControl.inc.php 2023-03-29
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project (earlier name of osCommerce)
   (c) 2002-2003 osCommerce (gv_send.php,v 1.1.2.3 2003/05/12); www.oscommerce.com
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: gv_send.php 1034 2005-07-15 15:21:43Z mz $)

   Released under the GNU General Public License
   -----------------------------------------------------------------------------------------
   Third Party contribution:

   Credit Class/Gift Vouchers/Discount Coupons (Version 5.10)
   http://www.oscommerce.com/community/contributions,282
   Copyright (c) Strider | Strider@oscworks.com
   Copyright (c  Nick Stanko of UkiDev.com, nick@ukidev.com
   Copyright (c) Andre ambidex@gmx.net
   Copyright (c) 2001,2002 Ian C Wilson http://www.phesis.org


   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

require_once(DIR_WS_CLASSES . 'http_client.php');
require_once(DIR_FS_INC . 'xtc_validate_email.inc.php');
require_once DIR_FS_INC . 'xtc_validate_email.inc.php';

MainFactory::load_class('DataProcessing');

/**
 * Class GVSendContentControl
 */
class GVSendContentControl extends DataProcessing
{
    protected $customers_status_id;
    protected $currency;
    protected $customer_id;
    protected $language;


    /**
     * GVSendContentControl constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }


    protected function set_validation_rules()
    {
        $this->validation_rules_array['customers_status_id'] = array('type' => 'int');
        $this->validation_rules_array['currency'] = array('type' => 'string');
        $this->validation_rules_array['customer_id'] = array('type' => 'int');
        $this->validation_rules_array['language'] = array('type' => 'string');
    }


    /**
     * @return bool
     */
    public function proceed()
    {
        $uninitializedVariables = $this->get_uninitialized_variables(array(
            'customers_status_id',
            'currency',
            'customer_id',
            'language'
        ));

        if (empty($uninitializedVariables)) {
            $languageTextManager = MainFactory::create('LanguageTextManager', 'gv_send');
            $xtPrice = new xtcPrice($this->currency, $this->customers_status_id);
            $gvSendContentView = MainFactory::create_object('GVSendThemeContentView');
            $action = '';

            if (($this->v_data_array['GET']['action'] ?? null) === 'send') {
                $error = false;

                $gvSendContentView->set_('error_email', '');
                if (!xtc_validate_email(trim($this->v_data_array['POST']['email']))) {
                    $error = true;
                    $gvSendContentView->set_('error_email', ERROR_ENTRY_EMAIL_ADDRESS_CHECK);
                }

                $query = 'SELECT
										amount
									FROM
										' . TABLE_COUPON_GV_CUSTOMER . '
									WHERE
										customer_id = "' . $this->customer_id . '"';
                $gvResult = xtc_db_query($query);
                $gvRow = xtc_db_num_rows($gvResult) ? xtc_db_fetch_array($gvResult) : ['amount' => 0];
                $customerAmount = $gvRow['amount'];
                $gvAmount = trim(str_replace(',', '.', $this->v_data_array['POST']['amount']));

                $gvSendContentView->set_('error_amount', '');

                if (preg_match('![^0-9\.]!', $gvAmount)) {
                    $error = true;
                    $gvSendContentView->set_('error_amount',
                        $languageTextManager->get_text('error_entry_amount_check_NAN'));
                } else {
                    $gvAmount = (double)$gvAmount;
                }

                if ($gvAmount > $customerAmount || $gvAmount == 0) {
                    $error = true;
                    $gvSendContentView->set_('error_amount',
                        $languageTextManager->get_text('error_entry_amount_check_OOR'));
                }

                $query = 'SELECT
									customers_firstname,
									customers_lastname
								FROM
									' . TABLE_CUSTOMERS . '
								WHERE
									customers_id = "' . $this->customer_id . '"';
                $gvResult = xtc_db_query($query);
                $gvRow = xtc_db_fetch_array($gvResult);
                $sendName = $gvRow['customers_firstname'] . ' ' . $gvRow['customers_lastname'];
                $gvSendContentView->set_('personal_message', sprintf(PERSONAL_MESSAGE, $gvRow['customers_firstname']));
                $gvSendContentView->set_('send_name', $sendName);
                $gvSendContentView->set_('main_message', sprintf(MAIN_MESSAGE, $xtPrice->xtcFormat(str_replace(",", ".",
                    htmlentities_wrapper($this->v_data_array['POST']['amount'])),
                    true),
                    stripslashes($this->v_data_array['POST']['to_name']),
                    $this->v_data_array['POST']['email'],
                    stripslashes($this->v_data_array['POST']['to_name']),
                    $xtPrice->xtcFormat(str_replace(",", ".",
                        $this->v_data_array['POST']['amount']),
                        true), $sendName));
                $gvSendContentView->set_('to_name', stripslashes($this->v_data_array['POST']['to_name']));
                $gvSendContentView->set_('email', $this->v_data_array['POST']['email']);
                $gvSendContentView->set_('message_body', stripslashes($this->v_data_array['POST']['message_body']));

                // validate entries
                $gvAmount = (double)$gvAmount;
                $gvSendContentView->set_('amount', (string)$gvAmount);

                if (!$error) {
                    $action = 'send';
                }
            } elseif (($this->v_data_array['GET']['action'] ?? null) === 'process') {
                require_once DIR_FS_INC . 'create_coupon_code.inc.php';
                
                $action = 'process';
                $couponCode = create_coupon_code();
                $query = 'SELECT
									amount
								FROM
									' . TABLE_COUPON_GV_CUSTOMER . '
								WHERE
									customer_id = "' . $this->customer_id . '"';
                $gvResult = xtc_db_query($query);
                $gvRow = xtc_db_num_rows($gvResult) ? xtc_db_fetch_array($gvResult) : ['amount' => 0];

                $customerAmount = $gvRow['amount'];
                $gvAmount = trim(str_replace(",", ".", $this->v_data_array['POST']['amount']));

                $gvSendContentView->set_('error_amount', '');
                $error = false;

                if (preg_match('![^0-9\.]!', $gvAmount)) {
                    $error = true;
                    $gvSendContentView->set_('error_amount',
                        $languageTextManager->get_text('error_entry_amount_check_NAN'));
                }

                $gvAmount = (double)$gvAmount;

                if ($gvAmount > $customerAmount || $gvAmount == 0) {
                    $error = true;
                    $gvSendContentView->set_('error_amount',
                        $languageTextManager->get_text('error_entry_amount_check_OOR'));
                }

                if ($error) {
                    $action = '';

                    $gvSendContentView->set_('amount', '');
                    $gvSendContentView->set_('error_email', '');
                    $gvSendContentView->set_('to_name', stripslashes($this->v_data_array['POST']['to_name']));
                    $gvSendContentView->set_('email', $this->v_data_array['POST']['email']);
                    $gvSendContentView->set_('message_body', stripslashes($this->v_data_array['POST']['message_body']));
                } else {
                    $new_amount = (double)$gvRow['amount'] - $gvAmount;

                    $query = 'UPDATE
									' . TABLE_COUPON_GV_CUSTOMER . '
								SET
									amount = "' . $new_amount . '"
								WHERE
									customer_id = "' . $this->customer_id . '"';
                    xtc_db_query($query);

                    $query = 'SELECT
										customers_firstname,
										customers_lastname
									FROM
										' . TABLE_CUSTOMERS . '
									WHERE
										customers_id = "' . $this->customer_id . '"';
                    $gvResult = xtc_db_query($query);
                    $gvCustomer = xtc_db_fetch_array($gvResult);

                    $query = 'INSERT INTO
										' . TABLE_COUPONS . '
									SET
										coupon_type		= "G",
										coupon_code		= "' . $couponCode . '",
										date_created	= NOW(),
										coupon_amount	= "' . $gvAmount . '"';
                    $gvResult = xtc_db_query($query);
                    $couponId = xtc_db_insert_id($gvResult);

                    $query = 'INSERT INTO
									' . TABLE_COUPON_EMAIL_TRACK . '
								SET
									coupon_id			= "' . $couponId . '",
									customer_id_sent	= "' . $this->customer_id . '",
									sent_firstname		= "' . xtc_db_input($gvCustomer['customers_firstname']) . '",
									sent_lastname		= "' . xtc_db_input($gvCustomer['customers_lastname']) . '",
									emailed_to			= "' . xtc_db_input($this->v_data_array['POST']['email']) . '",
									date_sent			= NOW()';
                    xtc_db_query($query);

                    $gvEmailSubject = sprintf(EMAIL_GV_TEXT_SUBJECT,
                        stripslashes($this->v_data_array['POST']['send_name']));

                    $smarty = MainFactory::create('GXSmarty');

                    $logoManager = MainFactory::create_object('GMLogoManager', array('gm_logo_mail'));
                    if ($logoManager->logo_use == '1') {
                        $smarty->assign('gm_logo_mail', $logoManager->get_logo());
                    }
                    $smarty->assign('language', $this->language);
                    $smarty->assign('tpl_path',
                        DIR_FS_CATALOG . StaticGXCoreLoader::getThemeControl()->getThemeHtmlPath());
                    $smarty->assign('logo_path',
                        HTTP_SERVER . DIR_WS_CATALOG . StaticGXCoreLoader::getThemeControl()->getThemeImagePath());
                    $giftLink = xtc_href_link(FILENAME_GV_REDEEM, 'gv_no=' . $couponCode, 'NONSSL', false);
                    $smarty->assign('GIFT_LINK', $giftLink);
                    $smarty->assign('AMMOUNT', $xtPrice->xtcFormat(str_replace(',', '.',
                        htmlentities_wrapper($this->v_data_array['POST']['amount'])),
                        true));
                    $smarty->assign('GIFT_CODE', $couponCode);
                    $smarty->assign('MESSAGE',
                        htmlentities_wrapper(gm_prepare_string($this->v_data_array['POST']['message_body'],
                            true)));
                    $smarty->assign('NAME',
                        htmlentities_wrapper(gm_prepare_string($this->v_data_array['POST']['to_name'],
                            true)));
                    $smarty->assign('FROM_NAME',
                        htmlentities_wrapper(gm_prepare_string($this->v_data_array['POST']['send_name'],
                            true)));

                    // dont allow cache
                    $smarty->caching = false;
    
                    if (defined('EMAIL_SIGNATURE') && defined('EMAIL_HTML_SIGNATURE')) {
                        $smarty->assign('EMAIL_SIGNATURE_HTML', EMAIL_HTML_SIGNATURE);
                        $smarty->assign('EMAIL_SIGNATURE_TEXT', EMAIL_SIGNATURE);
                    }

                    $htmlMail = fetch_email_template($smarty, 'send_gift_to_friend');
                    $giftLink = str_replace('&amp;', '&', $giftLink);
                    $smarty->assign('GIFT_LINK', $giftLink);
                    $txtMail = fetch_email_template($smarty, 'send_gift_to_friend', 'txt');

                    // send mail
                    xtc_php_mail(EMAIL_BILLING_ADDRESS, EMAIL_BILLING_NAME, $this->v_data_array['POST']['email'],
                        $this->v_data_array['POST']['to_name'], '', EMAIL_BILLING_REPLY_ADDRESS,
                        EMAIL_BILLING_REPLY_ADDRESS_NAME, '', '', $gvEmailSubject, $htmlMail, $txtMail);
                }
            } elseif (isset($this->v_data_array['GET']['action']) == false) {
                $gvSendContentView->set_('amount', '');
                $gvSendContentView->set_('error_amount', '');
                $gvSendContentView->set_('error_email', '');
                $gvSendContentView->set_('to_name', '');
                $gvSendContentView->set_('email', '');
                $gvSendContentView->set_('message_body', '');
            }

            $gvSelect = 'SELECT amount FROM ' . TABLE_COUPON_GV_CUSTOMER . ' WHERE customer_id = "'
                . $this->customer_id . '"';
            $gvQuery = xtc_db_query($gvSelect);
            $gvDeposit = xtc_db_num_rows($gvQuery) ? xtc_db_fetch_array($gvQuery) : ['amount' => 0];
            $gvSendContentView->set_('gvDeposit', $gvDeposit['amount']);
            $gvSendContentView->set_('gvDepositCurrency', $xtPrice->xtcFormat($gvDeposit['amount'], true, 0, true));

            $gvSendContentView->set_('action', $action);
            $this->v_output_buffer = $gvSendContentView->get_html();
        } else {
            trigger_error("Variable(s) " . implode(', ', $uninitializedVariables) . " do(es) not exist in class "
                . get_class($this) . " or are null", E_USER_ERROR);
        }

        return true;
    }
}
