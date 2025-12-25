<?php
/* --------------------------------------------------------------
   UsermodJSMaster.inc.php 2021-08-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class UsermodJSMaster
{
    public $v_page = '';

    public function __construct($p_page = 'Global')
    {
        $this->set_page($p_page);
    }


    public function set_page($p_page)
    {
        $this->v_page = basename($p_page);
    }


    public function get_page()
    {
        return $this->v_page;
    }
    
    
    /**
     * @param string $theme_id
     * @param string $group
     * @param array  $gxThemeFiles
     *
     * @return array
     */
    protected function get_theme_files(string $theme_id, string $group, array $gxThemeFiles){
        $t_files_array = [];
        //page scripts
        if(isset($gxThemeFiles[$theme_id][$group]['javascript'][strtolower($this->get_page())])){
            foreach ($gxThemeFiles[$theme_id][$group]['javascript'][strtolower($this->get_page())] as $file) {
                $t_files_array[] = $file;
            }
        }
        
        return $t_files_array;
    
    }
    
    
    public function get_files()
    {
        $dataCache = DataCache::get_instance();
        $cacheKey  = 'js_usermods-' . md5(StaticGXCoreLoader::getThemeControl()->getCurrentTheme()) . '-'. $this->get_page();
        
        if ($dataCache->key_exists($cacheKey, true)) {
            return $dataCache->get_data($cacheKey, true);
        }
        
        $includedFiles = [];
        $gxThemeFiles  = GXModulesCache::getInstalledThemeFiles();
        $themes        = array_reverse(StaticGXCoreLoader::getThemeControl()->getCurrentThemeHierarchy());
        $themes        = array_merge(['all'], array_map('strtolower', $themes));
        
        foreach ($themes as $currentTheme) {
            $includedFiles[] = $this->get_theme_files($currentTheme, 'core', $gxThemeFiles);
        }
        foreach ($themes as $currentTheme) {
            $includedFiles[] = $this->get_theme_files($currentTheme, 'custom', $gxThemeFiles);
        }
        $t_files_array = array_merge(...$includedFiles);
        $t_files_array = array_unique($t_files_array);
        
        $dataCache->set_data($cacheKey, $t_files_array, true);
        
        return $t_files_array;
    }
}
