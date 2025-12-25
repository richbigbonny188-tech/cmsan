<?php
/* --------------------------------------------------------------
 Configuration.php 2020-03-09
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Core\Configuration\Model\Interfaces;

/**
 * Interface Configuration
 * @package Gambio\Core\Configuration\Model\Interfaces
 */
interface Configuration
{
    /**
     * Returns the configuration key.
     *
     * @return string
     */
    public function key(): string;
    
    
    /**
     * Returns the configuration value.
     *
     * @return string|null
     */
    public function value(): ?string;
    
    
    /**
     * Returns the configuration sort order.
     *
     * @return int|null
     */
    public function sortOrder(): ?int;
}