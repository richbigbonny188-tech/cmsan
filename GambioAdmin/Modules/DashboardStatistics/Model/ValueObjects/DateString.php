<?php
/*--------------------------------------------------------------
   DateString.php 2021-09-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\DashboardStatistics\Model\ValueObjects;

use DateTime;

class DateString
{
    /**
     * @var string
     */
    private $date;
    
    /**
     * @var DateTime
     */
    private $dateTime;
    
    
    /**
     * Constructor.
     */
    public function __construct(string $date)
    {
        $this->date     = $date;
        $this->dateTime = new DateTime($date);
    }
    
    
    /**
     * Return value.
     */
    public function value(): string
    {
        return $this->date;
    }
    
    
    /**
     * Return as date-time.
     */
    public function asDateTime(): DateTime
    {
        return $this->dateTime;
    }
}