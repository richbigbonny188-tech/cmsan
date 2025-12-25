<?php
/*--------------------------------------------------------------
   ConversionDataProviderResult.php 2021-09-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\DashboardStatistics\Model\Entities;

use Gambio\Admin\Modules\DashboardStatistics\Model\Collections\Values;
use Gambio\Admin\Modules\DashboardStatistics\Model\ValueObjects\MinimumFractionDigits;
use Gambio\Admin\Modules\DashboardStatistics\Model\ValueObjects\Name;
use Gambio\Admin\Modules\DashboardStatistics\Model\ValueObjects\Style;
use Gambio\Admin\Modules\DashboardStatistics\Model\ValueObjects\Title;
use Gambio\Admin\Modules\DashboardStatistics\Model\ValueObjects\Unit;

class ConversionDataProviderResult extends DataProviderResult
{
    /**
     * Constructor.
     */
    public function __construct(
        Title $title,
        Unit $unit,
        Style $style,
        MinimumFractionDigits $minimumFractionDigits,
        Values $values
    ) {
        parent::__construct(new Name('conversions'), $title, $unit, $style, $minimumFractionDigits, $values);
    }
}