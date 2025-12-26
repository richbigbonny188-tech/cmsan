<?php
/*--------------------------------------------------------------
   Value.php 2021-09-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\DashboardStatistics\Model\Entities;

use Gambio\Admin\Modules\DashboardStatistics\Model\ValueObjects\DateString;
use JsonSerializable;

class Value
{
    /**
     * @var DateString
     */
    private $date;
    
    /**
     * @var JsonSerializable
     */
    private $value;
    
    
    /**
     * Constructor.
     */
    public function __construct(DateString $date, float $value)
    {
        $this->date  = $date;
        $this->value = $value;
    }
    
    
    /**
     * Return date.
     */
    public function date(): DateString
    {
        return $this->date;
    }
    
    
    /**
     * Return value.
     */
    public function value(): float
    {
        return $this->value;
    }
}