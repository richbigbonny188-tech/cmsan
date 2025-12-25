<?php
/*--------------------------------------------------------------------------------------------------
    ThemeContentImporterInterface.php 2019-10-17
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2019 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\StyleEdit\Adapters\Interfaces;

use Gambio\StyleEdit\Core\SingletonPrototype;

/**
 * Interface ThemeContentImporterInterface
 * @package Gambio\StyleEdit\Adapters\Interfaces
 */
interface ThemeContentImporterAdapterInterface
{
    
    
    /**
     * @param string $themeId
     *
     * @return mixed
     */
    public function importContentFromTheme(string $themeId);
    
}