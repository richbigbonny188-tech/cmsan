<?php
/* --------------------------------------------------------------
   CategoryDescriptionBottomThemeContentView.inc.php 2019-06-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class CategoryDescriptionBottomThemeContentView extends ThemeContentView
{
    public function __construct()
    {
        parent::__construct();
        
        $this->set_content_template('category_description_bottom.html');
        $this->set_flat_assigns(true);
    
        $this->set_content_data('CATEGORIES_ID', sha1($_SERVER['HTTP_HOST']. $_SERVER['REQUEST_URI']));
    }
}
