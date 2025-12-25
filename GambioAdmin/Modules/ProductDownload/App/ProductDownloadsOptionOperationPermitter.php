<?php
/* --------------------------------------------------------------
   ProductDownloadsOptionOperationPermitter.php 2023-06-26
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ProductDownload\App;

use Gambio\Admin\Modules\Option\Model\Option;
use Gambio\Admin\Modules\Option\Model\ValueObjects\OptionId;
use Gambio\Admin\Modules\Option\Services\OptionOperationPermitter;

/**
 * Class ProductDownloadsOptionOperationPermitter
 *
 * @package Gambio\Admin\Modules\ProductDownload\App
 * @deprecated Since 4.7, the gambio shop-system supports submodules. Those product domains were refactored into
 *             submodules too. This class will be deleted with 4.11.
 */
class ProductDownloadsOptionOperationPermitter implements OptionOperationPermitter
{
    public function __construct(
        private \Gambio\Admin\Modules\Product\Submodules\Download\App\ProductDownloadsOptionOperationPermitter $permitter
    ) {
    }
    
    
    /**
     * @inheritDoc
     */
    public function permitsCreations(array ...$creationArgs): bool
    {
        return $this->permitter->permitsCreations(...$creationArgs);
    }
    
    
    /**
     * @inheritDoc
     */
    public function permitsStorages(Option ...$options): bool
    {
        return $this->permitter->permitsStorages(...$options);
    }
    
    
    /**
     * @inheritDoc
     */
    public function permitsDeletions(OptionId ...$ids): bool
    {
        return $this->permitsDeletions(...$ids);
    }
}