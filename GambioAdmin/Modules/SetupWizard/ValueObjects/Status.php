<?php
/* --------------------------------------------------------------
 Status.php 2022-08-05
 Gambio GmbH
 http://www.gambio.de

 Copyright (c) 2022 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Modules\SetupWizard\ValueObjects;

use JsonSerializable;

/**
 * Class Status
 * @package Gambio\Admin\Modules\SetupWizard\ValueObjects
 */
class Status implements JsonSerializable
{
    /**
     * @var bool
     */
    protected $status;
    
    
    /**
     * Status constructor.
     *
     * @param bool $status
     */
    public function __construct(bool $status)
    {
        $this->status = $status;
    }
    
    
    /**
     * @return bool
     */
    public function value(): bool
    {
        return $this->status;
    }
    
    
    /**
     * @inheritDoc
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->status;
    }
}