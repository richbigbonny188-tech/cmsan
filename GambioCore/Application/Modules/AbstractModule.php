<?php
/* --------------------------------------------------------------
 AbstractModule.php 2020-10-26
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Core\Application\Modules;

/**
 * Class AbstractModule
 *
 * @package Gambio\Core\Application\Modules
 */
abstract class AbstractModule implements Module
{
    /**
     * @inheritDoc
     */
    public function eventListeners(): ?array
    {
        return null;
    }
    
    
    /**
     * @inheritDoc
     */
    public function dependsOn(): ?array
    {
        return null;
    }
    
    
    /**
     * @inheritDoc
     */
    public function shopMiddleware(): ?array
    {
        return null;
    }
    
    
    /**
     * @inheritDoc
     */
    public function adminMiddleware(): ?array
    {
        return null;
    }
    
    
    /**
     * @inheritDoc
     */
    public function apiMiddleware(): ?array
    {
        return null;
    }
}