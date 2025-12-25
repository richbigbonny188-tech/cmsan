<?php
/*--------------------------------------------------------------
   ProductOptionProductVariantOperationPermitter.php 2023-06-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ProductOption\App;

use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\App\AdditionalOptionProductVariantOperationPermitter;
use Gambio\Admin\Modules\ProductVariant\Model\ProductVariant;
use Gambio\Admin\Modules\ProductVariant\Model\ValueObjects\ProductVariantId;
use Gambio\Admin\Modules\ProductVariant\Services\ProductVariantOperationPermitter as ProductVariantOperationPermitterInterface;

/**
 * Class ProductVariantOperationPermitter
 *
 * @package    Gambio\Admin\Modules\ProductOption\App
 * @deprecated Since 4.7, the gambio shop-system supports submodules. Those product domains were refactored into
 *             submodules too. This class will be deleted with 4.11. Migrate usage of this class to
 *             \Gambio\Admin\Modules\ProductVariant\Services\ProductVariantOperationPermitter
 */
class ProductOptionProductVariantOperationPermitter implements ProductVariantOperationPermitterInterface
{
    public function __construct(private AdditionalOptionProductVariantOperationPermitter $permitter) { }
    
    
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
    public function permitsStorages(ProductVariant ...$variants): bool
    {
        return $this->permitter->permitsStorages(...$variants);
    }
    
    
    /**
     * @inheritDoc
     */
    public function permitsDeletions(ProductVariantId ...$ids): bool
    {
        return $this->permitter->permitsDeletions(...$ids);
    }
}