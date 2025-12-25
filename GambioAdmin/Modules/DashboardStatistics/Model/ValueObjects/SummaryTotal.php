<?php
/*------------------------------------------------------------------------------
 SummaryTotal.php 2021-09-20
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2021 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -----------------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\DashboardStatistics\Model\ValueObjects;

class SummaryTotal
{
    /**
     * @var string
     */
    private $name;
    
    /**
     * @var float
     */
    private $total;
    
    /**
     * @var float
     */
    private $change;
    
    
    /**
     * Constructor.
     */
    public function __construct(string $name, $total, $change)
    {
        $this->name   = $name;
        $this->total  = (float)(string)$total;
        $this->change = (float)(string)$change;
    }
    
    
    /**
     * Return name.
     */
    public function name(): string
    {
        return $this->name;
    }
    
    
    /**
     * Return total value.
     */
    public function total(): float
    {
        return $this->total;
    }
    
    
    /**
     * Return change value.
     */
    public function change(): float
    {
        return $this->change;
    }
}