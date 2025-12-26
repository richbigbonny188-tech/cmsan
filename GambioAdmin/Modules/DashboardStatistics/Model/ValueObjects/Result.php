<?php
/*------------------------------------------------------------------------------
 Result.php 2021-09-20
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2021 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -----------------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\DashboardStatistics\Model\ValueObjects;

use Gambio\Admin\Modules\DashboardStatistics\Model\Collections\Categories;
use Gambio\Admin\Modules\DashboardStatistics\Model\Collections\SummarizedDataItems;
use JsonSerializable;

class Result implements JsonSerializable
{
    /**
     * @var SummarizedDataItems
     */
    protected $statistics;
    
    /**
     * @var Categories
     */
    protected $categories;
    
    
    /**
     * Constructor.
     */
    public function __construct(
        SummarizedDataItems $statistics,
        Categories $categories
    ) {
        $this->statistics = $statistics;
        $this->categories = $categories;
    }
    
    
    /**
     * Return statistics.
     */
    public function statistics(): SummarizedDataItems
    {
        return $this->statistics;
    }
    
    
    /**
     * Return categories.
     */
    public function categories(): Categories
    {
        return $this->categories;
    }
    
    
    /**
     * @inheritDoc
     */
    public function jsonSerialize(): array
    {
        return [
            'categories' => $this->categories(),
            'statistics' => $this->statistics()
        ];
    }
}