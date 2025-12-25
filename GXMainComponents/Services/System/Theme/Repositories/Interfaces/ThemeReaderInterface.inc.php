<?php
/* --------------------------------------------------------------
   ThemeReaderInterface.inc.php 2018-11-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface ThemeReaderInterface
 */
interface ThemeReaderInterface
{
    /**
     * Returns a theme instance by the given id and source directory.
     *
     * @param \ThemeId                     $id     Theme id, the identifier is the theme name.
     * @param \ThemeDirectoryRootInterface $source Path to themes source directory.
     *
     * @return \ThemeInterface
     */
    public function getTheme(ThemeId $id, ThemeDirectoryRootInterface $source);
    
    
    /**
     * Returns a list of names of all available themes.
     *
     * @param \ThemeDirectoryRootInterface $source
     *
     * @return \ThemeNameCollection
     */
    public function getAvailableThemes(ThemeDirectoryRootInterface $source);
}
