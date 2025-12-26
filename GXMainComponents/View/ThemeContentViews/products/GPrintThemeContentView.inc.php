<?php
/* --------------------------------------------------------------
   GPrintThemeContentView.inc.php 2018-12-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class GPrintThemeContentView extends ThemeContentView
{
    public function __construct()
    {
        parent::__construct();
        
        $this->set_content_template('product_info_customizer.html');
        $this->set_flat_assigns(true);
    }
}