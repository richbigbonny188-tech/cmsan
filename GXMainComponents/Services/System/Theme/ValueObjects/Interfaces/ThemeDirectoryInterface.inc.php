<?php
/* --------------------------------------------------------------
   ThemeDirectoryInterface.php 2018-11-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface ThemeDirectoryInterface
 */
interface ThemeDirectoryInterface
{
    /**
     * Returns the theme directories root path.
     *
     * @return \ThemeDirectoryRootInterface
     */
    public function getRoot();
    
    
    /**
     * Returns a list of all files in the current theme (non-recursively).
     *
     * @return string[]
     */
    public function getFiles();
    
    
    /**
     * Returns a list of all directories in the current theme (non-recursively).
     *
     * @return \ThemeDirectoryCollection|null
     */
    public function getChildren();
}
