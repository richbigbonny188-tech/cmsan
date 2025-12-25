<?php
/*--------------------------------------------------------------------------------------------------
    IdentifiedThemeInterface.php 2019-06-06
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2016 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

/**
 * Interface IdentifiedThemeInterface
 */
interface IdentifiedThemeInterface extends BaseThemeDirectoriesInterface
{
    /**
     * Returns the theme id
     *
     * @return ThemeId
     */
    public function getId();
    
}