<?php
/*--------------------------------------------------------------
   PaginationMeta.php 2023-09-26
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Statistics\Submodules\SoldProduct\Model\ValueObjects;

/**
 * Class PaginationMeta
 *
 * @package Gambio\Admin\Modules\Statistics\Submodules\SoldProduct\Model\ValueObjects
 */
class PaginationMeta
{
    /**
     * @var int|mixed
     */
    private int $maxPage;
    
    
    /**
     * @var int|mixed
     */
    private int $from;
    
    
    /**
     * @var int|mixed
     */
    private int $to;
    
    
    /**
     * PaginationMeta constructor.
     *
     * @param int $page
     * @param int $perPage
     * @param int $totalItems
     */
    public function __construct(private int $page, private int $perPage, private int $totalItems)
    {
        $maxPage       = (int)ceil($totalItems / $perPage);
        $this->maxPage = max($maxPage, 1);
        
        $from       = $page * $perPage - $this->perPage + 1;
        $this->from = min(max($from, 1), $this->totalItems);
        
        $to       = $page * $perPage;
        $this->to = max(min($to, $totalItems), 1);
    }
    
    
    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'page'       => $this->page,
            'perPage'    => $this->perPage,
            'totalItems' => $this->totalItems,
            'maxPage'    => $this->maxPage,
            'from'       => $this->from,
            'to'         => $this->to,
        ];
    }
}