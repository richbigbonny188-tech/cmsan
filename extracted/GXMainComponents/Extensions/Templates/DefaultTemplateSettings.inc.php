<?php

/* --------------------------------------------------------------
   DefaultTemplateSettings.inc.php 2016-08-01 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   -------------------------------------------------------------- 
*/

/**
 * Class DefaultTemplateSettings
 *
 * Class to provide the default settings of a shop frontend template.
 * Used for overload able template settings.
 *
 * For overloading some of the template settings do as follows:
 *
 * - create a overload of the DefaultTemplateSettings class
 * - create a overload of the setTemplateSettings method:
 *
 *      public function setTemplateSettingsArray (array $settingsArray)
 *      {
 *          $settingsArray['MENUBOXES']['your_new_menu_box'] =  array('POSITION' => 'gm_box_pos_99', 'STATUS' => 0);
 *          $this->templateSettingsArray = $settingsArray;
 *      }
 *
 * @category   System
 * @package    Extensions
 * @subpackage Templates
 */
class DefaultTemplateSettings
{
    
    /**
     * @var array
     */
    protected $templateSettingsArray = [];
    
    
    /**
     * Getter method
     *
     * Returns the settings of a template as an array.
     *
     * @return array
     */
    public function getTemplateSettingsArray()
    {
        return $this->templateSettingsArray;
    }
    
    
    /**
     * Setter method
     *
     * Sets the templateSettingsArray to the given values.
     *
     * You can use something like this to manipulate the settingsArray:
     *
     * public function setTemplateSettingsArray (array $settingsArray)
     * {
     *     $settingsArray['MENUBOXES']['your_new_menu_box'] =  array('POSITION' => 'gm_box_pos_99', 'STATUS' => 0);
     *     $this->templateSettingsArray = $settingsArray;
     * }
     *
     * @param array $settingsArray
     */
    public function setTemplateSettingsArray(array $settingsArray)
    {
        $this->templateSettingsArray = $settingsArray;
    }
}