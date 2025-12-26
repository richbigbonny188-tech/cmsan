<?php
/* --------------------------------------------------------------
   GiftVouchersStatusThemeContentView.inc.php 2019-08-26
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class GiftVouchersStatusThemeContentView extends ThemeContentView
{
    public function __construct()
    {
        parent::__construct();
        
        $this->set_content_template('checkout_success_giftvouchersstatus.html');
        $this->set_flat_assigns(true);
        $this->set_caching_enabled(false);
    }
    
}
