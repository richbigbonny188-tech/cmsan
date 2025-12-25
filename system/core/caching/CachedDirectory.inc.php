<?php
/* --------------------------------------------------------------
  CachedDirectory.inc.php 2021-08-16
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2021 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

class CachedDirectory
{
    private static $s_config_cache_paths_array = array();
    public $v_directory_content_array = array();
    public $v_cache_key = '';
    public $v_count_index = 0;
    public $v_path = '';
    public $v_coo_cache;

    protected $directoryBlacklist = ['node_modules'];

    /*
     * constructor
     */
    public function __construct($p_path)
    {
        $this->v_coo_cache = DataCache::get_instance();
        $currentTemplate = $this->_getCurrentTheme();

        if (!isset(self::$s_config_cache_paths_array[$currentTemplate])) {
            $shopPath = realpath(DIR_FS_CATALOG);
            $shopPath = str_replace('\\', '/', $shopPath) . '/';
            
            $t_cache_paths_array = array(
                $shopPath . 'admin/includes/classes',
                $shopPath . 'admin/includes/gm/classes',
                $shopPath . 'gm/classes',
                $shopPath . 'gm/javascript',
                $shopPath . 'gm/properties',
                $shopPath . 'includes/classes',
                $shopPath . 'includes/modules/order_total',
                $shopPath . 'includes/modules/payment',
                $shopPath . 'includes/modules/shipping',
                $shopPath . 'system/classes',
                $shopPath . 'system/core',
                $shopPath . 'system/extender',
                $shopPath . 'system/overloads',
                $shopPath . 'GXEngine',
                $shopPath . 'GXMainComponents',
                $shopPath . 'GXModules'
            );

            if (file_exists($shopPath . 'PdfCreator/tcpdf.php')) {
                $t_cache_paths_array[] = $shopPath . 'PdfCreator';
            }

            foreach ($t_cache_paths_array as $t_key => $t_dir) {
                if (is_dir($t_dir) === false) {
                    unset($t_cache_paths_array[$t_key]);
                }
            }

            self::$s_config_cache_paths_array[$currentTemplate] = $t_cache_paths_array;
        }

        $this->set_cache_key('directory_cache');
        $this->set_path($p_path);
        $this->load_cache();
    }


    public function set_path($p_path)
    {
        if (check_data_type($p_path, 'string') && is_readable($p_path)) {
            $this->v_path = $this->filter_path(realpath($p_path));

            $this->reset_count_index();

            return true;
        }

        return false;
    }


    public function get_path()
    {
        return $this->v_path;
    }


    public function set_cache_key($p_filename)
    {
        if (check_data_type($p_filename, 'string')) {
            $currentTemplate = $this->_getCurrentTheme();

            $this->v_cache_key = basename($p_filename . '_' . $currentTemplate);

            return true;
        }

        return false;
    }


    public function get_cache_key()
    {
        return basename((string)$this->v_cache_key);
    }


    public function set_directory_content_array($p_directory_content_array)
    {
        if (check_data_type($p_directory_content_array, 'array')) {
            $this->v_directory_content_array = $p_directory_content_array;

            return true;
        }

        return false;
    }


    public function read()
    {
        if (isset($this->v_directory_content_array[$this->get_path()][$this->v_count_index])) {
            $this->v_count_index++;

            return $this->v_directory_content_array[$this->get_path()][$this->v_count_index - 1];
        }

        return false;
    }


    public function reset_count_index()
    {
        $this->v_count_index = 0;
    }


    public function clear_cache()
    {

        $this->v_coo_cache->clear_cache($this->get_cache_key());

        return true;
    }


    public function rebuild_cache()
    {
        $currentTemplate = $this->_getCurrentTheme();

        foreach (self::$s_config_cache_paths_array[$currentTemplate] AS $t_path) {
            $this->scan_dir($t_path);
        }

        $this->v_coo_cache = DataCache::get_instance();
        $this->v_coo_cache->set_data($this->get_cache_key(), $this->v_directory_content_array, true);
    }


    public function scan_dir($p_path)
    {
        if (check_data_type($p_path, 'string') && is_readable($this->filter_path($p_path))) {
            $c_path = $this->filter_path($p_path);
            $t_path_pattern = $c_path . '/*';

            $t_glob_data_array = glob($t_path_pattern);

            if (is_array($t_glob_data_array)) {
                foreach ($t_glob_data_array as $t_result) {
                    $t_entry = basename($t_result);

                    if ($t_entry[0] === '.' || in_array($t_entry, $this->directoryBlacklist, true)) {
                        continue;
                    }

                    $t_part = '/';
                    if (substr($c_path, -1) === $t_part) {
                        $t_part = '';
                    }

                    if (!isset($this->v_directory_content_array[$c_path])
                        || !is_array($this->v_directory_content_array[$c_path])) {
                        $this->v_directory_content_array[$c_path] = array();
                    }

                    if(!in_array($t_entry, $this->v_directory_content_array[$c_path], true)) {
                        $this->v_directory_content_array[$c_path][] = $t_entry;
                    }
                    
                    if (is_dir($c_path . '/' . $t_entry)) {
                        $this->scan_dir($c_path . $t_part . $t_entry);
                    }
                }
            }

            return true;
        } else {
            trigger_error('CachedDirectory scan_dir failed, because p_path is not a valid absolute path: '
                . (string)$p_path, E_USER_ERROR);
        }

        return false;
    }


    public function load_cache()
    {
        $this->v_coo_cache = DataCache::get_instance();

        if ($this->v_coo_cache->key_exists($this->get_cache_key(), true)) {
            $t_serialized_cache_data_array = $this->v_coo_cache->get_data($this->get_cache_key());

            if (check_data_type($t_serialized_cache_data_array, 'array')) {
                return $this->set_directory_content_array($t_serialized_cache_data_array);
            }
        } else {
            $this->rebuild_cache();
        }

        return false;
    }


    public function is_dir($p_path)
    {
        $c_path = $this->filter_path((string)$p_path);

        if ($p_path !== '/' && substr($p_path, -1) === '/') {
            $c_path = substr($c_path, 0, -1);
        }

        if (isset($this->v_directory_content_array[$c_path])) {
            return true;
        }

        return false;
    }


    public function is_file($p_path)
    {
        return !$this->is_dir($p_path);
    }


    public function file_exists($p_file_path)
    {
        $c_file_path = $this->filter_path($p_file_path);

        if (isset($this->v_directory_content_array[dirname($this->filter_path($c_file_path))])
            && in_array(basename($c_file_path),
                $this->v_directory_content_array[$this->filter_path(dirname($c_file_path))], true)) {
            return true;
        } else {
            $t_real_file_path = @realpath($c_file_path);

            if (!empty($t_real_file_path)) {
                if (isset($this->v_directory_content_array[dirname(str_replace('\\', '/', $t_real_file_path))])
                    && in_array(basename($c_file_path),
                        $this->v_directory_content_array[dirname(str_replace('\\', '/', $t_real_file_path))],
                        true)) {
                    return true;
                }

                if (file_exists($t_real_file_path)) {
                    return true;
                }

                return false;
            } elseif (file_exists($c_file_path)) {
                return true;
            }

            return false;
        }
    }


    protected function filter_path($p_path)
    {
        return str_replace(['\\', '//'], '/', $p_path);
    }

    protected function _getCurrentTheme()
    {
        static $previewThemeHash, $currentThemeHash;
        
        if (defined('PREVIEW_MODE') && PREVIEW_MODE) {
            return $previewThemeHash = $previewThemeHash ?? md5(PREVIEW_THEME);
        }
        
        if (defined('CURRENT_THEME') && !empty(CURRENT_THEME)) {
            return $currentThemeHash = $currentThemeHash ?? md5(CURRENT_THEME);
        }

        return 'template_blank';
    }
}
