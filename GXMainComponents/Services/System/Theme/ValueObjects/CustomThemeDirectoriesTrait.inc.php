<?php

/*--------------------------------------------------------------------------------------------------
    CustomThemeDirectoriesTrait.php 2019-06-06
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2016 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

/**
 * Trait CustomThemeDirectoriesTrait
 */
trait CustomThemeDirectoriesTrait
{
    
    /**
     * Custom HTML directory
     *
     * @var ThemeDirectory|null
     */
    protected $customHtml;
    
    /**
     * Custom JS directory
     *
     * @var ThemeDirectory|null
     */
    protected $customJs;
    
    /**
     * Custom styles directory
     *
     * @var ThemeDirectory|null
     */
    protected $customStyles;
    
    /**
     * script extension directory
     *
     * @var ThemeDirectory|null
     */
    protected $jsExtensions;
    
    
    /**
     * Return the custom HTML directory
     *
     * @return ThemeDirectory|null
     */
    public function getCustomHtml()
    {
        return $this->customHtml;
    }
    
    
    /**
     * Return the custom scripts directory
     *
     * @return ThemeDirectory|null
     */
    public function getCustomJs()
    {
        return $this->customJs;
    }
    
    
    /**
     * Return the custom styles directory
     *
     * @return ThemeDirectory|null
     */
    public function getCustomStyles()
    {
        return $this->customStyles;
    }
    
    
    /**
     * Returns the script extension directory
     *
     * @return ThemeDirectory|null
     */
    public function getJsExtensions()
    {
        return $this->jsExtensions;
    }
}