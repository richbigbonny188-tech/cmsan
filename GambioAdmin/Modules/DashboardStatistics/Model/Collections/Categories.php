<?php
/*------------------------------------------------------------------------------
 Categories.php 2021-09-20
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2021 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -----------------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\DashboardStatistics\Model\Collections;

use Gambio\Admin\Modules\DashboardStatistics\Model\ValueObjects\Category;
use Gambio\Admin\Modules\DashboardStatistics\Support\AbstractCollection;
use JsonSerializable;

class Categories extends AbstractCollection implements JsonSerializable
{
    /**
     * @inheritDoc
     */
    public function current(): Category
    {
        return $this->currentValue();
    }
    
    
    /**
     * @inheritDoc
     */
    protected function isValid($value): bool
    {
        return $value instanceof Category;
    }
    
    
    /**
     * @inheritDoc
     */
    public function jsonSerialize(): array
    {
        $result = [];
        
        /** @var Category $category */
        foreach ($this->values as $category) {
            $result[$category->name()->value()] = [
                'title'                 => $category->title()->value(),
                'unit'                  => $category->unit()->value(),
                'style'                 => $category->style()->value(),
                'minimumFractionDigits' => $category->minimumFractionDigits()->value(),
            ];
        }
        
        return $result;
    }
}