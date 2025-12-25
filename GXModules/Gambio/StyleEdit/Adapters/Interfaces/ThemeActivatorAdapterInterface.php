<?php
/*--------------------------------------------------------------------------------------------------
    ThemeActivatorAdapterInterface.php 2019-10-18
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2019 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */
namespace Gambio\StyleEdit\Adapters\Interfaces;

/**
 * Interface ThemeActivatorAdapterInterface
 * @package Gambio\StyleEdit\Adapters\Interfaces
 */
interface ThemeActivatorAdapterInterface
{
    /**
     * @param string $themeId
     *
     * @return mixed
     */
    public function activateTheme(string $themeId);
    
}