<?php
/* --------------------------------------------------------------
   DefaultThemeSettings.inc.php 2018-11-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   -------------------------------------------------------------- 
*/

/**
 * Class DefaultThemeSettings
 *
 * Class to provide the default settings of a shop frontend theme.
 * Used for overload able theme settings.
 *
 * For overloading some of the theme settings do as follows:
 *
 * - create a overload of the DefaultThemeSettings class
 * - create a overload of the setThemeSettings method:
 *
 *      public function setThemeSettingsArray (array $settingsArray)
 *      {
 *          $settingsArray['MENUBOXES']['your_new_menu_box'] =  array('POSITION' => 'gm_box_pos_99', 'STATUS' => 0);
 *          $this->themeSettingsArray = $settingsArray;
 *      }
 *
 * @category   System
 * @package    Extensions
 * @subpackage Themes
 */
class DefaultThemeSettings
{
    /**
     * @var array
     */
    protected $themeSettingsArray = [];
    
    
    /**
     * Getter method
     *
     * Returns the settings of a theme as an array.
     *
     * @return array
     */
    public function getThemeSettingsArray()
    {
        return $this->themeSettingsArray;
    }
    
    
    /**
     * Setter method
     *
     * Sets the themeSettingsArray to the given values.
     *
     * You can use something like this to manipulate the settingsArray:
     *
     * public function setThemeSettingsArray (array $settingsArray)
     * {
     *     $settingsArray['MENUBOXES']['your_new_menu_box'] =  array('POSITION' => 'gm_box_pos_99', 'STATUS' => 0);
     *     $this->themeSettingsArray = $settingsArray;
     * }
     *
     * @param array $settingsArray
     */
    public function setThemeSettingsArray(array $settingsArray)
    {
        $this->themeSettingsArray = $settingsArray;
    }
}