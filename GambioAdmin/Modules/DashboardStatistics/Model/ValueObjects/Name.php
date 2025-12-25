<?php
/* --------------------------------------------------------------
 Name.php 2021-09-20
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2021 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Modules\DashboardStatistics\Model\ValueObjects;

use JsonSerializable;

class Name implements JsonSerializable
{
    /**
     * @var string
     */
    protected $name;
    
    
    /**
     * Constructor.
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }
    
    
    /**
     * Return value.
     */
    public function value(): string
    {
        return $this->name;
    }
    
    
    /**
     * @inheritDoc
     */
    public function jsonSerialize(): string
    {
        return $this->value();
    }
}