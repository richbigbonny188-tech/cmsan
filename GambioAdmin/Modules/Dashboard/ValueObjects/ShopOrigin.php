<?php
/*--------------------------------------------------------------
   ShopOrigin.php 2022-08-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Dashboard\ValueObjects;

use JsonSerializable;

/**
 * Class ShopOrigin
 * @package Gambio\Admin\Modules\Dashboard\ValueObjects
 */
class ShopOrigin implements JsonSerializable
{
    public const KEY_CLOUD      = 'cloud';
    public const KEY_FREE       = 'free';
    public const KEY_ON_PREMISE = 'onpremise';
    
    /**
     * @var string
     */
    protected $shopOrigin;
    
    
    /**
     * ShopOrigin constructor.
     *
     * @param string $shopOrigin
     */
    public function __construct(string $shopOrigin)
    {
        $this->shopOrigin = $shopOrigin;
    }
    
    
    /**
     * @return string
     */
    public function value(): string
    {
        return $this->shopOrigin;
    }
    
    
    /**
     * @inheritDoc
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->value();
    }
}