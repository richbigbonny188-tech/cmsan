<?php
/*------------------------------------------------------------------------------
 SummaryPartial.php 2021-09-20
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2021 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -----------------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\DashboardStatistics\Model\ValueObjects;

class SummaryPartial
{
    /**
     * @var PartialLabel
     */
    private $label;
    
    /**
     * @var PartialValue[]
     */
    private $values;
    
    
    /**
     * Constructor.
     */
    public function __construct(
        PartialLabel $label,
        PartialValue ...$values
    ) {
        $this->label  = $label;
        $this->values = $values;
    }
    
    
    /**
     * Return label.
     */
    public function label(): PartialLabel
    {
        return $this->label;
    }
    
    
    /**
     * Return values.
     */
    public function values(): array
    {
        return $this->values;
    }
}