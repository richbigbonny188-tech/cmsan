<?php
/*--------------------------------------------------------------
   AdditionalOptionFilterServiceProxy.php 2023-06-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\App;

use Gambio\Admin\Modules\Product\Services\AdditionalOptionFilterService as AdditionalOptionFilterServiceInterface;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\Collections\AdditionalOptions;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services as Submodule;

/**
 * Class AdditionalOptionFilterServiceProxy
 *
 * @package Gambio\Admin\Modules\Product\App\Proxies
 */
class AdditionalOptionFilterService implements AdditionalOptionFilterServiceInterface
{
    public function __construct(private Submodule\AdditionalOptionFilterService $submoduleFilterService) { }
    
    
    /**
     * @inheritDoc
     */
    public function filterAdditionalOptions(
        int     $productId,
        array   $filters,
        ?string $sorting = null,
        int     $limit = 25,
        int     $offset = 0
    ): AdditionalOptions {
        return $this->submoduleFilterService->filterAdditionalOptions($productId,
                                                                      $filters,
                                                                      $sorting,
                                                                      $limit,
                                                                      $offset);
    }
    
    
    /**
     * @inheritDoc
     */
    public function getAdditionalOptionsTotalCount(int $productId, array $filters): int
    {
        return $this->submoduleFilterService->getAdditionalOptionsTotalCount($productId, $filters);
    }
}