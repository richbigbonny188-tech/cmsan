<?php
/* --------------------------------------------------------------
   JSOptionsSource.inc.php 2023-01-25
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class JSOptionsSource
{
	public $v_js_options_path;
	public $v_js_options_array = array();

	public function __construct()
	{
		$this->v_js_options_path = DIR_FS_CATALOG.'system/conf/js_options/';
	}

	public function init_structure_array( $p_get_array )
	{
		if(file_exists($this->v_js_options_path . 'global_options.php'))
		{
			include($this->v_js_options_path . 'global_options.php');
		}
		
		$t_directory = opendir($this->v_js_options_path);
		while ($t_file = readdir($t_directory)){
			if($t_file != '.' && $t_file != '..' && substr($t_file, -4) == '.php' && $t_file != 'global_options.php'){
				include($this->v_js_options_path.$t_file);
			}
		}
        
		//$array = $this->utf8_encode_mix($array);
		$this->v_js_options_array = $array;
	}
	
	public function get_array(){
		return $this->v_js_options_array;
	}
    
    public function get_js_options_path(){
        return $this->v_js_options_path;
    }
    
    public function set_js_options_path($p_js_options_path){
        $t_js_options_path = (string)$p_js_options_path;
        $this->v_js_options_path = $t_js_options_path;
    }
    
    public function set_js_options_user_classes_path($p_js_options_user_classes_path){
        $t_js_options_user_classes_path = (string)$p_js_options_user_classes_path;
    }
    
    public function utf8_encode_mix($input, $encode_keys=false)
    {
        if(is_array($input))
        {
            $result = array();
            foreach($input as $k => $v)
            {
				$key = ($encode_keys)? mb_convert_encoding($k, 'UTF-8', 'ISO-8859-1') : $k;
                $result[$key] = $this->utf8_encode_mix( $v, $encode_keys);
            }
        }
        else if(is_string($input))
        {
            $result = mb_convert_encoding($input, 'UTF-8', 'ISO-8859-1');
        }
		else
		{
			$result = $input;
		}

        return $result;
    } 
}