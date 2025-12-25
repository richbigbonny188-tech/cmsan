<?php
/* --------------------------------------------------------------
   CallbackServiceThemeContentView.inc.php 2023-02-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

use GXModules\Gambio\AntiSpam\Shop\classes\AntiSpamUtilityTrait;

class CallbackServiceThemeContentView extends ThemeContentView
{
    use AntiSpamUtilityTrait;
    
    public function __construct()
    {
        parent::__construct();
        
        $this->set_content_template('callback_service.html');
        $this->set_flat_assigns(true);
    }
    
    
    /**
     * @return void
     */
    public function prepare_data()
    {
        $this->set_content_data('secret_token_anti_spam', $this->generateSecretAntiSpamToken());
        $this->set_content_data('fake_hash', sha1(random_bytes(256)));
        
        parent::prepare_data();
    }
}