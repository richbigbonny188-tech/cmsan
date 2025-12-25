<?php
/* --------------------------------------------------------------
   ThemeDirectoryRootInterface.inc.php 2019-06-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface ThemeDirectoryRootInterface
 */
interface ThemeDirectoryRootInterface
{
    /**
     * Returns an absolute path, which represents a theme directory.
     *
     * @return string
     */
    public function getPath();
    
    
    /**
     * @param string $path path to be checked
     *
     * @return mixed
     */
    public function hasPath($path);
    
    
    /**
     * Appends the given path to the internal theme path and returns a new ThemeDirectoryRootInterface instance.
     *
     * @param string $path Path to append.
     *
     * @return \ThemeDirectoryRootInterface New instance.
     */
    public function withPath($path);
}
