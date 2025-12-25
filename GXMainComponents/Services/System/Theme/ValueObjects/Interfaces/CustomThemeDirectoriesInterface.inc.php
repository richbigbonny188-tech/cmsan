<?php
/*--------------------------------------------------------------------------------------------------
    CustomThemeDirectoriesInterface.inc.php 2019-06-06
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2016 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

/**
 * Interface CustomThemeDirectoriesInterface
 */
interface CustomThemeDirectoriesInterface
{
    /**
     * Returns the custom HTML directory.
     *
     * @return ThemeDirectory|null
     */
    public function getCustomHtml();
    
    
    /**
     * Returns the custom scripts directory.
     *
     * @return ThemeDirectory|null
     */
    public function getCustomJs();
    
    
    /**
     * Returns the custom styles directory.
     *
     * @return ThemeDirectory|null
     */
    public function getCustomStyles();
    
    
    /**
     * Returns the script extension directory
     *
     * @return ThemeDirectory|null
     */
    public function getJsExtensions();
    
}