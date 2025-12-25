<?php

/* --------------------------------------------------------------
   ThemeInterface.php 2019-06-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the MIT License
   [https://opensource.org/licenses/MIT]
   --------------------------------------------------------------
*/

/**
 * Interface ThemeInterface
 */
interface ThemeInterface extends ThemeDirectoriesInterface, IdentifiedThemeInterface
{
    /**
     * Return whether there is a parent theme
     *
     * @return bool
     */
    public function hasParent();
    
    
    /**
     * Convert to main theme
     *
     * @return \MainThemeInterface
     */
    public function toMainTheme();
    
    
    /**
     * Return the parent theme
     *
     * @return ThemeInterface|null
     */
    public function getParentTheme();
}