<?php
/* --------------------------------------------------------------
   PopupNotificationThemeContentView.inc.php 2018-11-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class PopupNotificationThemeContentView
 */
class PopupNotificationThemeContentView extends ThemeContentView
{
    public function __construct()
    {
        parent::__construct();
        
        $this->set_content_template('popup_notification.html');
    }
    
    
    public function prepare_data()
    {
        /** @var PopupNotificationReader $popupNotificationReader */
        $popupNotificationReader = MainFactory::create_object('PopupNotificationReader');
        
        /** @var PopupNotification $popupNotification */
        $popupNotification = $popupNotificationReader->getPopupNotification();
        
        $this->set_content_data('popupNotification', $popupNotification);
    }
}
