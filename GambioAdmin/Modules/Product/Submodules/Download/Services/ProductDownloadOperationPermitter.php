<?php
/*--------------------------------------------------------------
   ProductDownloadOperationPermitter.php 2023-06-21
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\Download\Services;

use Gambio\Admin\Modules\Product\Submodules\Download\Model\ProductDownload;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\ValueObjects\AdditionalOptionId;

/**
 * Interface ProductDownloadOperationPermitter
 *
 * @package Gambio\Admin\Modules\Product\Submodules\Download\Services
 */
interface ProductDownloadOperationPermitter
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
     * @param ProductDownload ...$productOption
     *
     * @return bool
     */
    public function permitsStorages(ProductDownload ...$productOption): bool;
    
    
    /**
     * Checks the permission of the delete operation.
     *
     * @param AdditionalOptionId ...$ids
     *
     * @return bool
     */
    public function permitsDeletions(AdditionalOptionId ...$ids): bool;
}