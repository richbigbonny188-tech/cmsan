<?php
/*--------------------------------------------------------------------
 AdditionalOptionFilterService.php 2023-06-06
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2023 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services;

use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\Collections\AdditionalOptions;

/**
 * Interface AdditionalOptionFilterService
 *
 * @package Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services
 */
interface AdditionalOptionFilterService
{
    /**
     * @param int         $productId
     * @param array       $filters
     * @param string|null $sorting
     * @param int         $limit
     * @param int         $offset
     *
     * @return AdditionalOptions
     */
    public function filterAdditionalOptions(
        int     $productId,
        array   $filters,
        ?string $sorting = null,
        int     $limit = 25,
        int     $offset = 0
    ): AdditionalOptions;
    
    
    /**
     * @param int   $productId
     * @param array $filters
     *
     * @return int
     */
    public function getAdditionalOptionsTotalCount(
        int   $productId,
        array $filters
    ): int;
}