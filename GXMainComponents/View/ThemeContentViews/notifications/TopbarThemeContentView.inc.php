<?php
/* --------------------------------------------------------------
   TopbarThemeContentView.inc.php 2018-11-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class TopbarThemeContentView
 */
class TopbarThemeContentView extends ThemeContentView
{
    public function __construct()
    {
        parent::__construct();
        
        $this->set_content_template('topbar.html');
    }
    
    
    public function prepare_data()
    {
        /** @var TopbarNotificationReader $topbarNotificationReader */
        $topbarNotificationReader = MainFactory::create_object('TopbarNotificationReader');
        
        /** @var TopbarNotification $topbarNotification */
        $topbarNotification = $topbarNotificationReader->getTopbarNotification();
        
        $this->set_content_data('topbarNotification', $topbarNotification);
    }
}
