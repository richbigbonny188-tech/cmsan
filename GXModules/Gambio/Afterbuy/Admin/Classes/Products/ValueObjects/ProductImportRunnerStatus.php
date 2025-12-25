<?php
/* --------------------------------------------------------------
   ProductImportRunnerStatus.php 2023-10-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);

namespace GXModules\Gambio\Afterbuy\Admin\Classes\Products\ValueObjects;

use DateTimeInterface;

/**
 * Class ProductImportRunnerStatus
 *
 * @package GXModules\Gambio\Afterbuy\Admin\Classes\Products\ValueObjects
 */
class ProductImportRunnerStatus
{
    /**
     * @var DateTimeInterface
     */
    private DateTimeInterface $since;
    
    
    /**
     * @var int
     */
    private int $lastProductId;
    
    
    /**
     * @var int
     */
    private int $currentPage;
    
    
    /**
     * @var int
     */
    private int $totalPages;
    
    
    /**
     * @param DateTimeInterface $since
     * @param int               $lastProductId
     * @param int               $currentPage
     * @param int               $totalPages
     */
    public function __construct(DateTimeInterface $since, int $lastProductId, int $currentPage = 0, int $totalPages = 0)
    {
        $this->since         = $since;
        $this->lastProductId = $lastProductId;
        $this->currentPage   = $currentPage;
        $this->totalPages    = $totalPages;
    }
    
    
    /**
     * @return DateTimeInterface
     */
    public function getSince(): DateTimeInterface
    {
        return $this->since;
    }
    
    
    /**
     * @return int
     */
    public function getLastProductId(): int
    {
        return $this->lastProductId;
    }
    
    
    /**
     * @return int
     */
    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }
    
    
    /**
     * @return int
     */
    public function getTotalPages(): int
    {
        return $this->totalPages;
    }
    
    
}