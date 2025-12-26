<?php
/*------------------------------------------------------------------------------
 SummaryTitle.php 2021-09-20
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2021 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -----------------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\DashboardStatistics\Model\ValueObjects;

class SummaryTitle
{
    /**
     * @var string
     */
    private $title;
    
    
    /**
     * Constructor.
     */
    public function __construct(string $title)
    {
        $this->title = $title;
    }
    
    
    /**
     * Return title.
     */
    public function title(): string
    {
        return $this->title;
    }
    
}