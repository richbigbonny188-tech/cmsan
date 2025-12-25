<?php
/*--------------------------------------------------------------
  Unit.php 2021-09-20
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2021 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\DashboardStatistics\Model\ValueObjects;

use JsonSerializable;

class Unit implements JsonSerializable
{
    /**
     * @var string
     */
    private $unit;
    
    
    /**
     * Constructor.
     */
    public function __construct(string $unit)
    {
        $this->unit = $unit;
    }
    
    
    /**
     * Return value.
     */
    public function value(): string
    {
        return $this->unit;
    }
    
    
    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return $this->value();
    }
}