<?php
/*--------------------------------------------------------------------
 AdditionalOptionReadService.php 2023-06-07
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2023 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\AdditionalOption\App;

use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\AdditionalOption;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\Collections\AdditionalOptions;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services\AdditionalOptionFactory;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services\AdditionalOptionReadService as AdditionalOptionReadServiceInterface;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services\AdditionalOptionRepository as AdditionalOptionRepositoryInterface;

/**
 * Class AdditionalOptionReadService
 *
 * @package Gambio\Admin\Modules\Product\Submodules\AdditionalOption\App
 */
class AdditionalOptionReadService implements AdditionalOptionReadServiceInterface
{
    /**
     * AdditionalOptionReadService constructor.
     *
     * @param AdditionalOptionRepositoryInterface $repository
     * @param AdditionalOptionFactory             $factory
     */
    public function __construct(
        private AdditionalOptionRepositoryInterface $repository,
        private AdditionalOptionFactory             $factory
    ) {
    }
    
    
    /**
     * @inheritDoc
     */
    public function getAdditionalOptionsByProductId(int $productId): AdditionalOptions
    {
        return $this->repository->getAdditionalOptionsByProductId($this->factory->createProductId($productId));
    }
    
    
    /**
     * @inheritDoc
     */
    public function getAdditionalOptionById(int $additionalOptionId): AdditionalOption
    {
        return $this->repository->getAdditionalOptionById($this->factory->createAdditionalOptionId($additionalOptionId));
    }
}