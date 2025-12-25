<?php
/* --------------------------------------------------------------
   PayPalBoxThemeContentView.inc.php 2018-11-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommercebased on original files FROM OSCommerce CVS 2.2 2002/08/28 02:14:35 www.oscommerce.com
   (c) 2003	 nextcommerce (admin.php,v 1.12 2003/08/13); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: admin.php 1262 2005-09-30 10:00:32Z mz $)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

class PayPalBoxThemeContentView extends ThemeContentView
{
    public function __construct()
    {
        parent::__construct();
        $this->set_content_template('box_paypal.html');
    }
    
    
    public function prepare_data()
    {
        $this->build_html = false;
        
        $t_query = 'SELECT
						`value`
					FROM
						`gx_configurations`
					WHERE
						`value` = "true"
						AND (`key` = "configuration/MODULE_PAYMENT_PAYPALEXPRESS_STATUS"
							OR
							`key` = "configuration/MODULE_PAYMENT_PAYPAL_STATUS"
							OR
							`key` = "configuration/MODULE_PAYMENT_PAYPALNG_STATUS"
							OR
							`key` = "configuration/MODULE_PAYMENT_PAYPAL3_STATUS")';
        
        $t_result = xtc_db_query($t_query);
        if (xtc_db_num_rows($t_result) > 0 || StyleEditServiceFactory::service()->isEditing()) {
            $this->content_array['PAYPAL_URL'] = 'https://www.paypal.com/de/webapps/mpp/pay-online';
            $this->build_html                  = true;
        }
    }
}
