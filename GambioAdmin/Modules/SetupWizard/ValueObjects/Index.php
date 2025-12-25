<?php
/* --------------------------------------------------------------
 Index.php 2022-08-05
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
 * Class Index
 * @package Gambio\Admin\Modules\SetupWizard\ValueObjects
 */
class Index implements JsonSerializable
{
    
    /**
     * @var int
     */
    protected $index;
    
    
    /**
     * Index constructor.
     *
     * @param int $index
     */
    public function __construct(int $index)
    {
        $this->index = $index;
    }
    
    
    /**
     * @return int
     */
    public function value(): int
    {
        return $this->index;
    }
    
    
    /**
     * @inheritDoc
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->index;
    }
}