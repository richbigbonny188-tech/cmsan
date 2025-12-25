<?php
/*--------------------------------------------------------------------------------------------------
    BasicThemeInterface.php 2019-11-29
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2019 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */
namespace Gambio\StyleEdit\Core\Components\Theme\Entities\Interfaces;

/**
 * Interface BasicThemeInterface
 * @package Gambio\StyleEdit\Core\Components\Theme\Entities\Interfaces
 */
interface BasicThemeInterface
{
    /**
     * @return string
     */
    public function id(): string;
    
    
    /**
     * @return BasicThemeInterface|null
     */
    public function parent() : ?BasicThemeInterface;
    
}