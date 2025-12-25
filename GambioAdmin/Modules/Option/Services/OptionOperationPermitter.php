<?php
/* --------------------------------------------------------------
   OptionOperationPermitter.php 2020-03-26
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Option\Services;

use Gambio\Admin\Modules\Option\Model\Option;
use Gambio\Admin\Modules\Option\Model\ValueObjects\OptionId;

/**
 * Interface OptionOperationPermitter
 *
 * @package Gambio\Admin\Modules\Option\Services
 *
 * @deprecated Since 4.7, the gambio shop-system supports submodules. Those product domains will be refactored into
 *             submodules too. All important changes will be documented in the developer journal as soon as they are
 *             implemented.
 */
interface OptionOperationPermitter
{
    /**
     * Checks the permission of the create operation.
     *
     * @param array ...$creationArgs
     *
     * @return bool
     */
    public function permitsCreations(array ...$creationArgs): bool;
    
    
    /**
     * Checks the permission of the store operation.
     *
     * @param Option ...$options
     *
     * @return bool
     */
    public function permitsStorages(Option ...$options): bool;
    
    
    /**
     * Checks the permission of the delete operation.
     *
     * @param OptionId ...$ids
     *
     * @return bool
     */
    public function permitsDeletions(OptionId ...$ids): bool;
}