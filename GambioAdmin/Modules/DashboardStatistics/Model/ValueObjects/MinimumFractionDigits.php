<?php
/*--------------------------------------------------------------
  MinimumFractionDigits.php 2021-09-20
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2021 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\DashboardStatistics\Model\ValueObjects;

use JsonSerializable;

class MinimumFractionDigits implements JsonSerializable
{
    /**
     * @var string
     */
    private $minimumFractionDigits;
    
    
    /**
     * Constructor.
     */
    public function __construct(int $minimumFractionDigits)
    {
        $this->minimumFractionDigits = $minimumFractionDigits;
    }
    
    
    /**
     * Return value.
     */
    public function value(): int
    {
        return $this->minimumFractionDigits;
    }
    
    
    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return $this->value();
    }
}