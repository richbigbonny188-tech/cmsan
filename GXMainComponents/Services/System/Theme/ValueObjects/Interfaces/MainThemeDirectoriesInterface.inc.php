<?php
/* --------------------------------------------------------------
   MainThemeDirectoriesInterface.inc.php 2019-06-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface MainThemeDirectoriesInterface
 */
interface MainThemeDirectoriesInterface extends VariableThemeDirectoriesInterface, BaseThemeDirectoriesInterface
{
    
    /**
     * Returns the fonts theme directory.
     *
     * @return \ThemeDirectoryInterface
     */
    public function getFonts();
    
    
    /**
     * Returns the directory root.
     *
     * @return ThemeDirectoryRootInterface
     */
    public function getRoot();
    
}
