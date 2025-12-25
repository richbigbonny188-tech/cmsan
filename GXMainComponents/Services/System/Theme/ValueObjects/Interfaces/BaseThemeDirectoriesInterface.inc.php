<?php
/*--------------------------------------------------------------------------------------------------
    BaseThemeDirectoriesInterface.php 2019-06-06
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2016 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

/**
 * Interface BaseThemeDirectoriesInterface
 */
interface BaseThemeDirectoriesInterface
{
    
    
    /**
     * Returns the html theme directory.
     *
     * @return \ThemeDirectoryInterface
     */
    public function getHtml();
    
    
    /**
     * Returns the js theme directory.
     *
     * @return \ThemeDirectoryInterface
     */
    public function getJs();
    
    
    /**
     * Returns the styles theme directory.
     *
     * @return \ThemeDirectoryInterface
     */
    public function getStyles();
    
    
    /**
     * Returns the images directory.
     *
     * @return \ThemeDirectory|null
     */
    public function getImages();
    
    
    /**
     * @return string
     */
    public function getPrefix();
    
    
    /**
     * Returns the directory root.
     *
     * @return ThemeDirectoryRootInterface
     */
    public function getRoot();
    
    
    /**
     * Returns the fonts directory.
     *
     * @return \ThemeDirectory|null
     */
    public function getFonts();
    
}