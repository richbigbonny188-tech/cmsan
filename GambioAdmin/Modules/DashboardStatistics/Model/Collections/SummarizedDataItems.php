<?php
/*------------------------------------------------------------------------------
 SummarizedDataItems.php 2021-09-20
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2021 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -----------------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\DashboardStatistics\Model\Collections;

use Gambio\Admin\Modules\DashboardStatistics\Model\ValueObjects\SummarizedData;
use Gambio\Admin\Modules\DashboardStatistics\Support\AbstractCollection;

class SummarizedDataItems extends AbstractCollection
{
    /**
     * @inheritDoc
     */
    public function current(): SummarizedData
    {
        return $this->currentValue();
    }
    
    
    /**
     * @inheritDoc
     */
    protected function isValid($value): bool
    {
        return $value instanceof SummarizedData;
    }
    
    
    /**
     * @inheritDoc
     */
    public function jsonSerialize(): array
    {
        /**
         * @var SummarizedData $summarizedData
         */
        
        $result = [];
        
        foreach ($this->values as $summarizedData) {
            $result[$summarizedData->name()->value()] = $summarizedData;
        }
        
        return $result;
    }
}