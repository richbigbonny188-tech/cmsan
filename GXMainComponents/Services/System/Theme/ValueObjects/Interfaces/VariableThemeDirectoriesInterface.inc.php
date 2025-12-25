<?php
/*--------------------------------------------------------------------------------------------------
    VariableThemeDirectoriesInterface.php 2019-06-06
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2016 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

/**
 * Interface VariableThemeDirectoriesInterface
 */
interface VariableThemeDirectoriesInterface
{
    
    /**
     * Returns the variants directory
     *
     * @return ThemeDirectory|null
     */
    public function getVariants();
    
    
    /**
     * Returns the style edit theme directory.
     *
     * @return \ThemeDirectory|null
     */
    public function getStyleEdit();
    
    
    /**
     * Returns the config theme directory.
     *
     * @return \ThemeDirectoryInterface
     */
    public function getConfig();
    
}