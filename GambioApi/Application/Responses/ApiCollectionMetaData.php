<?php
/* --------------------------------------------------------------
   ApiCollectionMetaData.php 2022-08-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Api\Application\Responses;

use Gambio\Api\Application\Responses\Interfaces\ApiMetaData;

/**
 * Class ApiCollectionMetaData
 *
 * @package Gambio\Api\Application\Responses
 */
class ApiCollectionMetaData implements Interfaces\ApiCollectionMetaData
{
    /**
     * @var int
     */
    private $page;
    
    /**
     * @var int
     */
    private $perPage;
    
    /**
     * @var int
     */
    private $totalItems;
    
    /**
     * @var string[]
     */
    private $links;
    
    
    /**
     * ApiCollectionMetaData constructor.
     *
     * @param int   $page
     * @param int   $perPage
     * @param int   $totalItems
     * @param array $links
     */
    private function __construct(int $page, int $perPage, int $totalItems, array $links)
    {
        $this->page       = $page;
        $this->perPage    = $perPage;
        $this->totalItems = $totalItems;
        $this->links      = $links;
    }
    
    
    /**
     * @param int   $page
     * @param int   $perPage
     * @param int   $totalItems
     * @param array $links
     *
     * @return ApiCollectionMetaData
     */
    public static function create(
        int   $page,
        int   $perPage,
        int   $totalItems,
        array $links = []
    ): ApiCollectionMetaData {
        return new self($page, $perPage, $totalItems, $links);
    }
    
    
    /**
     * @param string $url
     * @param array  $queryParams
     *
     * @return $this
     */
    public function addPaginationLinks(string $url, array $queryParams): self
    {
        $url      = rtrim($url, '?') . '?';
        $lastPage = (int)ceil($this->totalItems / $this->perPage);
        
        if ($this->page > 1) {
            $this->setLink('firstPage', $url . http_build_query(array_merge($queryParams, ['page' => 1])));
            $this->setLink('previousPage',
                           $url . http_build_query(array_merge($queryParams, ['page' => $this->page - 1])));
        }
        if ($this->page < $lastPage) {
            $this->setLink('nextPage', $url . http_build_query(array_merge($queryParams, ['page' => $this->page + 1])));
            $this->setLink('lastPage', $url . http_build_query(array_merge($queryParams, ['page' => $lastPage])));
        }
        
        return $this;
    }
    
    
    /**
     * @inheritDoc
     */
    public function setPage(int $page): Interfaces\ApiCollectionMetaData
    {
        $this->page = $page;
        
        return $this;
    }
    
    
    /**
     * @inheritDoc
     */
    public function setPerPage(int $perPage): Interfaces\ApiCollectionMetaData
    {
        $this->perPage = $perPage;
        
        return $this;
    }
    
    
    /**
     * @inheritDoc
     */
    public function setTotalItems(int $totalItems): Interfaces\ApiCollectionMetaData
    {
        $this->totalItems = $totalItems;
        
        return $this;
    }
    
    
    /**
     * @inheritDoc
     */
    public function setLink(string $name, string $link): ApiMetaData
    {
        $this->links[$name] = $link;
        
        return $this;
    }
    
    
    /**
     * @inheritDoc
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        $totalPages = (int)ceil($this->totalItems / $this->perPage);
        
        return [
            'page'       => $this->page,
            'perPage'    => $this->perPage,
            'totalItems' => $this->totalItems,
            'totalPages' => $totalPages,
            'links'      => $this->links,
        ];
    }
}