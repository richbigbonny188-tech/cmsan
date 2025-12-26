<?php
/* --------------------------------------------------------------
   ThemeSettingsInterface.inc.php 2018-11-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface ThemeSettingsInterface
 */
interface ThemeSettingsInterface
{
    /**
     * Returns the path to the root of the theme's source directory.
     *
     * @return \ThemeDirectoryRootInterface
     */
    public function getSource();
    
    
    /**
     * Returns the path to the root of the theme's destination directory.
     *
     * @return \ThemeDirectoryRootInterface
     */
    public function getDestination();
}