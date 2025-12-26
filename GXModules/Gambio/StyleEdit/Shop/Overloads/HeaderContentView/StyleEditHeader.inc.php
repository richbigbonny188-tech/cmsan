<?php

/* --------------------------------------------------------------
   StyleEditHeader.inc.php 2019-05-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class StyleEditHeader
 * @codeCoverageIgnore
 */
class StyleEditHeader extends StyleEditHeader_parent
{
    /**
     * Prepare data
     */
    public function prepare_data()
    {
        // if parameter is present then set the cookie
        // and also check if the cookie is set
        $styleedit_mode = $_GET['styleedit'];
        if (isset($styleedit_mode) && $styleedit_mode == 1) {
            
            if (true) {
                $this->content_array['styleedit_active'] = true;
            }
        }
        
        parent::prepare_data();
    }
}