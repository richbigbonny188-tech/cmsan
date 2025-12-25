<?php
/* --------------------------------------------------------------
   ComponentsRegistry.php 2020-10-26
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\GXModules\Model;

use Gambio\Core\GXModules\Model\ValueObjects\GXModuleComponent;

/**
 * Interface ComponentsRegistry
 *
 * @package Gambio\Core\GXModules\Model
 */
interface ComponentsRegistry
{
    /**
     * Returns a collection of components
     *
     * @return GXModuleComponent[]
     */
    public function components(): array;
}