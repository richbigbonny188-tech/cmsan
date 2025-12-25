<?php
/* --------------------------------------------------------------
   CreateApiMetaDataTrait.php 2021-05-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Api\Application\Responses;

/**
 * Trait CreateApiMetaDataTrait
 *
 * @package Gambio\Api\Application\Responses
 */
trait CreateApiMetaDataTrait
{
    /**
     * @param int         $page
     * @param int         $perPage
     * @param int         $totalItems
     * @param string|null $resourcesUrl
     * @param array|null  $queryParams
     *
     * @return ApiCollectionMetaData
     */
    private function createApiCollectionMetaData(
        int $page,
        int $perPage,
        int $totalItems,
        string $resourcesUrl = null,
        array $queryParams = null
    ): ApiCollectionMetaData {
        $metaData = ApiCollectionMetaData::create($page, $perPage, $totalItems);
        if ($resourcesUrl !== null && $queryParams !== null) {
            $metaData->addPaginationLinks($resourcesUrl, $queryParams);
        }
        
        return $metaData;
    }
    
    
    /**
     * @param array $links
     *
     * @return ApiMetaData
     */
    private function createApiMetaData(array $links = []): ApiMetaData
    {
        return ApiMetaData::create($links);
    }
}