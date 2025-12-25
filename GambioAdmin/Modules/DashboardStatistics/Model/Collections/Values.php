<?php
/*--------------------------------------------------------------
   Values.php 2021-09-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\DashboardStatistics\Model\Collections;

use Gambio\Admin\Modules\DashboardStatistics\Model\Entities\Value;
use Gambio\Admin\Modules\DashboardStatistics\Support\AbstractCollection;

class Values extends AbstractCollection
{
    /**
     * @inheritDoc
     */
    public function current(): Value
    {
        return $this->currentValue();
    }
    
    
    /**
     * @inheritDoc
     */
    protected function isValid($value): bool
    {
        return $value instanceof Value;
    }
}