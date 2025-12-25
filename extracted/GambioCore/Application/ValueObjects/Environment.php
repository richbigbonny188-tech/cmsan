<?php
/* --------------------------------------------------------------
 Environment.php 2023-05-09
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Core\Application\ValueObjects;

/**
 * Class Environment
 *
 * @package Gambio\Core\Application\ValueObjects
 */
class Environment
{
    /**
     * Environment constructor.
     *
     * @param bool $isDev
     * @param bool $isCloud
     * @param bool $isEndToEnd
     */
    public function __construct(
        private bool $isDev,
        private bool $isCloud,
        private bool $isEndToEnd
    ) {
    }
    
    
    /**
     * Return true if develop mode is active.
     *
     * @return bool
     */
    public function isDev(): bool
    {
        return $this->isDev;
    }
    
    
    /**
     * Return true if current environment is a cloud environment.
     * 
     * @return bool
     */
    public function isCloud(): bool
    {
        return $this->isCloud;
    }
    
    
    /**
     * Return true if current environment is a end to end test environment.
     *
     * @return bool
     */
    public function isEndToEnd(): bool
    {
        return $this->isEndToEnd;
    }
}