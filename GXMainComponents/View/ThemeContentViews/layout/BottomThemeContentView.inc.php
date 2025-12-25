<?php
/* --------------------------------------------------------------
  BottomThemeContentView.inc.php 2018-11-13
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2018 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

class BottomThemeContentView extends ThemeContentView
{
    protected $parse_time;
    protected $extender_html;
    
    
    public function __construct()
    {
        parent::__construct();
        
        $this->set_content_template('layout_bottom.html');
    }
    
    
    public function prepare_data()
    {
        
        $developmentEnvironment = file_exists(DIR_FS_CATALOG . '.dev-environment');
        
        if ($developmentEnvironment && $_SESSION['customers_status']['customers_status_id'] === '0'
            && !isset($_GET['hide_debug_bar'])) {
            // Enable the debug bar. 
            $this->content_array['debug_bar']              = true;
            $debugBarAssets                                = StaticGXCoreLoader::getDebugBarAssets();
            $this->content_array['debug_bar_body_content'] = $debugBarAssets['body'];
        } else {
            // Disable the debug bar.
            $this->content_array['debug_bar'] = false;
        }
        
        $t_uninitialized_array = $this->get_uninitialized_variables(['extender_html']);
        if (empty($t_uninitialized_array)) {
            $t_content_html = '';
            
            if ($this->parse_time !== null) {
                $this->content_array['PARSE_TIME'] = $this->parse_time;
            }
            
            $t_content_html .= $this->get_modules_html();
            
            $t_content_html .= $this->extender_html;
            
            $this->content_array['CONTENT'] = $t_content_html;
        } else {
            trigger_error("Variable(s) " . implode(', ',
                                                   $t_uninitialized_array) . " do(es) not exist in class "
                          . get_class($this) . " or is/are null",
                          E_USER_ERROR);
        }
    }
    
    
    function get_modules_html()
    {
        return '';
    }
    
    
    protected function set_validation_rules()
    {
        $this->validation_rules_array['parse_time']    = ['type' => 'string', 'strict' => 'true'];
        $this->validation_rules_array['extender_html'] = ['type' => 'string', 'strict' => 'true'];
    }
}
