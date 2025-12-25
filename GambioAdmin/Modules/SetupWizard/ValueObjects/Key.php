<?php
/* --------------------------------------------------------------
 Key.php 2022-08-05
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
 * Class Key
 * @package Gambio\Admin\Modules\SetupWizard\ValueObjects
 */
class Key implements JsonSerializable
{
    /**
     * @var string
     */
    protected $key;
    
    
    /**
     * Key constructor.
     *
     * @param string $key
     */
    public function __construct(string $key)
    {
        $this->key = $key;
    }
    
    
    /**
     * @return string
     */
    public function value(): string
    {
        return $this->key;
    }
    
    
    /**
     * @inheritDoc
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->key;
    }
}