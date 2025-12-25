<?php
/*------------------------------------------------------------------------------
 PartialValue.php 2021-09-20
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2021 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -----------------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\DashboardStatistics\Model\ValueObjects;

class PartialValue
{
    /**
     * @var string
     */
    private $title;
    
    /**
     * @var float
     */
    private $value;
    
    
    /**
     * Constructor.
     */
    public function __construct(string $title, $value)
    {
        
        $this->title = $title;
        $this->value = (float)(string)$value;
    }
    
    
    /**
     * Return title.
     */
    public function title(): string
    {
        return $this->title;
    }
    
    
    /**
     * Return value.
     */
    public function value(): float
    {
        return $this->value;
    }
    
}