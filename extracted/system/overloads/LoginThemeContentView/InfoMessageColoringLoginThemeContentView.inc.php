<?php
/* --------------------------------------------------------------
   InfoMessageColoringLoginThemeContentView.inc.php 2018-12-12
   Gambio GmbH
   http://www.gambio.de
   Copyright © 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class InfoMessageColoringLoginContentView
 *
 * Set a message type for success messages to give them a proper styling
 */
class InfoMessageColoringLoginThemeContentView extends InfoMessageColoringLoginThemeContentView_parent
{
    /**
     * Prepare data
     */
    public function prepare_data()
    {
        parent::prepare_data();

        $this->content_array['message_type'] = 'danger';
        if ($this->info_message === SUCCESS_PASSWORD_UPDATED || $this->info_message === TEXT_PASSWORD_SAVED) {
            $this->content_array['message_type'] = 'success';
        }
    }
}