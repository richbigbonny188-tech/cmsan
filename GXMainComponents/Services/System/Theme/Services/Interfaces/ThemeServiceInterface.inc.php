<?php
/* --------------------------------------------------------------
   ThemeServiceInterface.inc.php 2019-08-28
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ThemeServiceInterface
 */
interface ThemeServiceInterface
{
    /**
     * Get Available themes.
     *
     * @param \ThemeDirectoryRootInterface $source
     *
     * @return \ThemeNameCollection
     */
    public function getAvailableThemes(ThemeDirectoryRootInterface $source);
    
    
    /**
     * build a temporary theme.
     *
     * @param \ThemeId                $themeId
     * @param \ThemeSettingsInterface $settings
     *
     * @return void
     */
    public function buildTemporaryTheme(ThemeId $themeId, ThemeSettingsInterface $settings);
    
    
    /**
     * Activates a theme for the shop.
     *
     * @param string $themeName
     *
     * @throws \Exception
     */
    public function activateTheme($themeName);
    
    
    /**
     * Stores the theme contents.
     *
     * @param \ThemeId       $themeId
     * @param \ThemeContents $themeContents
     *
     * @throws \Exception
     */
    public function storeThemeContent(ThemeId $themeId, ThemeContents $themeContents);
}