<?php
/* --------------------------------------------------------------
   TemplateContol.inc.php 2021-05-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of TemplateControl
 *
 * first load:    $coo_template_control = MainFactory::create_object('TemplateControl', array(CURRENT_THEME), true);
 * later use:    $coo_template_control = MainFactory::create_object('TemplateControl', array(), true);
 *
 * @author ncapuno
 */
class TemplateControl
{
    public $v_optional_template_settings_array = array();

    # set in load_template()
    protected $v_template_name = '';
    protected $v_template_presentation_version = '';

    /**
     * @deprecated since GX 4.5
     * @var string 
     */
    protected $v_template_container = '';
    
    protected $v_coo_boxes_master = false;

    /**
     * @var DataCache
     */
    protected $dataCache;

    protected $templateSettings = array();

    public static function &get_instance($p_template_name = false)
    {
        static $s_instance;

        if ($s_instance === null) {
            if ($p_template_name === false) {
                trigger_error('need template-parameter for creating instance', E_USER_ERROR);
                die();
            }
            $s_instance = MainFactory::create_object('TemplateControl', array($p_template_name));
        }
        return $s_instance;
    }

    public function __construct($p_template_name)
    {
        $this->dataCache = DataCache::get_instance();
        $this->_loadTemplateSettings();

        if (isset($GLOBALS['gmBoxesMaster'])) {
            $this->v_coo_boxes_master = $GLOBALS['gmBoxesMaster'];
        } else {
            $this->v_coo_boxes_master = MainFactory::create_object('SimpleBoxesMaster');
        }

        $this->load_template($p_template_name);
    }

    public function load_template($p_template_name)
    {
        $this->v_template_name = $p_template_name;
        $this->v_optional_template_settings_array = StaticGXCoreLoader::getThemeControl()->getThemeSettings();
        $this->v_template_presentation_version = StaticGXCoreLoader::getThemeControl()->getThemeVersion();
    }

    public function get_menubox_status($p_menubox)
    {

        if (StyleEditServiceFactory::service()->isEditing()) {
            # show all boxes, if style_edit is running
            return 1;
        }

        $key = 'boxStatus-' . $p_menubox;

        if (array_key_exists($key, $this->templateSettings)) {
            return $this->templateSettings[$key];
        }
        $status = $this->v_coo_boxes_master->get_status($p_menubox);
        $this->_writeCache($key, $status);

        return $status;
    }

    public function get_menubox_position($p_menubox)
    {
        $key = 'boxPosition-' . $p_menubox;

        if (array_key_exists($key, $this->templateSettings) && !StyleEditServiceFactory::service()->isEditing()) {
            return $this->templateSettings[$key];
        }

        $position = $this->v_coo_boxes_master->get_position($p_menubox);
        $this->_writeCache($key, $position);

        return $position;
    }

    public function get_template_name()
    {
        return $this->v_template_name;
    }

    public function get_template_presentation_version()
    {
        return $this->v_template_presentation_version;
    }

    public function reset_boxes_master()
    {
        if (isset($GLOBALS['gmBoxesMaster'])) {
            $this->v_coo_boxes_master = $GLOBALS['gmBoxesMaster'];
        } else {
            $this->v_coo_boxes_master = MainFactory::create_object('SimpleBoxesMaster');
        }
    }

    public function findSettingValueByName($p_settingName)
    {
        if (array_key_exists($p_settingName, $this->templateSettings)) {
            return $this->templateSettings[$p_settingName];
        }

        $value = $this->v_coo_boxes_master->findSettingValueByName($p_settingName);
        $this->_writeCache($p_settingName, $value);

        return $value;
    }


    protected function _loadTemplateSettings()
    {
        $templateSettings = array();
        $fileDate = 0;

        if ($this->dataCache->key_exists('template_settings', true)) {
            $templateSettings = $this->dataCache->get_persistent_data('template_settings');

            if (!is_array($templateSettings)) {
                $templateSettings = array();
            }
        }

        if (file_exists(DIR_FS_CATALOG . StaticGXCoreLoader::getThemeControl()->getTemplateSettingsFilePath())) {
            $fileDate = @filemtime(DIR_FS_CATALOG . StaticGXCoreLoader::getThemeControl()->getTemplateSettingsFilePath());
        }

        $cssCache = StyleEditServiceFactory::service()->getCacheFilePath() ?? DIR_FS_CATALOG . 'cache/__dynamics.css';

        if (file_exists($cssCache)) {
            $cssFileDate = @filemtime($cssCache);

            if ($cssFileDate > $fileDate) {
                $fileDate = $cssFileDate;
            }
        }

        if (array_key_exists('modification_date', $templateSettings)
            && $templateSettings['modification_date'] === $fileDate) {
            $this->templateSettings = $templateSettings;
        } else {
            $this->templateSettings['modification_date'] = $fileDate;
        }
    }


    protected function _writeCache($key, $value)
    {
        $this->templateSettings[$key] = $value;
        $this->dataCache->set_data('template_settings', $this->templateSettings, true);
    }
    
    
}
