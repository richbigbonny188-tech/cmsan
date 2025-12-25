<?php
/*--------------------------------------------------------------
   AdditionalOptionOperationPermitter.php 2023-06-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services;

use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\AdditionalOption;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects\AdditionalOptionId;

/**
 * Interface AdditionalOptionOperationPermitter
 *
 * @package Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services;
 */
interface AdditionalOptionOperationPermitter
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
     * @param AdditionalOption ...$additionalOption
     *
     * @return bool
     */
    public function permitsStorages(AdditionalOption ...$additionalOption): bool;
    
    
    /**
     * Checks the permission of the delete operation.
     *
     * @param AdditionalOptionId ...$ids
     *
     * @return bool
     */
    public function permitsDeletions(AdditionalOptionId ...$ids): bool;
}