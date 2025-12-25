<?php
/* --------------------------------------------------------------
   ApiCollectionMetaData.php 2020-02-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Api\Application\Responses\Interfaces;

/**
 * Interface ApiCollectionMetaData
 *
 * @package Gambio\Api\Application\Responses\Interfaces
 */
interface ApiCollectionMetaData extends ApiMetaData
{
    /**
     * @param int $page
     *
     * @return $this
     */
    public function setPage(int $page): self;
    
    
    /**
     * @param int $perPage
     *
     * @return $this
     */
    public function setPerPage(int $perPage): self;
    
    
    /**
     * @param int $totalItems
     *
     * @return $this
     */
    public function setTotalItems(int $totalItems): self;
}