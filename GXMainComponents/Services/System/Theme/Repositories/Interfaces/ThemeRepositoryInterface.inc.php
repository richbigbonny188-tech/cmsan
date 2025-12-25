<?php
/* --------------------------------------------------------------
   ThemeRepositoryInterface.inc.php 2018-11-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface ThemeRepositoryInterface
 */
interface ThemeRepositoryInterface
{
    /**
     * Returns the theme by given id.
     *
     * @param \ThemeId                     $id
     * @param \ThemeDirectoryRootInterface $source
     *
     * @return \ThemeInterface
     */
    public function getById(ThemeId $id, ThemeDirectoryRootInterface $source);
    
    
    /**
     * Returns available themes as collection.
     *
     * @param \ThemeDirectoryRootInterface $source
     *
     * @return \ThemeNameCollection
     */
    public function getAvailableThemes(ThemeDirectoryRootInterface $source);
    
    
    /**
     * Saves the theme.
     *
     * @param \ThemeInterface              $theme
     * @param \ThemeDirectoryRootInterface $destination
     *
     * @return void
     */
    public function save(ThemeInterface $theme, ThemeDirectoryRootInterface $destination);
}