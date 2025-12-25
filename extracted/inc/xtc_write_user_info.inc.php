<?php
/* --------------------------------------------------------------
   xtc_write_user_info.inc.php 2021-07-26
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------

   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(general.php,v 1.225 2003/05/29); www.oscommerce.com
   (c) 2003	 nextcommerce (xtc_write_user_info.inc.php,v 1.4 2003/08/13); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: xtc_write_user_info.inc.php 899 2005-04-29 02:40:57Z hhgag $)

   Released under the GNU General Public License
   --------------------------------------------------------------
*/

function xtc_write_user_info($customer_id)
{
    if (gm_get_conf('GM_LOG_IP_LOGIN') != 1) {
        return -1;
    }
    
    $sql_data_array = [
        'customers_id'          => $customer_id,
        'customers_ip'          => $_SESSION['tracking']['ip'] ?? '',
        'customers_ip_date'     => 'now()',
        'customers_host'        => $_SESSION['tracking']['http_referer']['host'] ?? '',
        'customers_advertiser'  => $_SESSION['tracking']['refID'] ?? '',
        'customers_referer_url' => isset($_SESSION['tracking']['http_referer']['host'], $_SESSION['tracking']['http_referer']['path']) ? $_SESSION['tracking']['http_referer']['host']
                                                                                                                                         . $_SESSION['tracking']['http_referer']['path'] : '',
    ];
    
    xtc_db_perform(TABLE_CUSTOMERS_IP, $sql_data_array);
    
    return -1;
}
