<?php
/*--------------------------------------------------------------------------------------------------
    AdditionalInfo.php 2021-03-04
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2021 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

/*--------------------------------------------------------------------------------------------------
    ModifierHint.php 2020-3-19
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2016 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\Shop\ProductModifiers\Modifiers\ValueObjects;


class AdditionalInfo
{
    /**
     * @var string
     */
    protected $pricePrefix;
    /**
     * @var float
     */
    protected $price;
    /**
     * @var bool
     */
    protected $showStock;
    
    
    /**
     * ModifierHint constructor.
     *
     * @param string $pricePrefix
     * @param float  $price
     * @param bool   $showStock
     */
    public function __construct(string $pricePrefix, float $price, bool $showStock)
    {
        $this->pricePrefix = $pricePrefix;
        $this->price       = $price;
        $this->showStock   = $showStock;
    }

    /**
     * @return string
     */
    public function pricePrefix(): string
    {
        return $this->pricePrefix;
    }

    /**
     * @return float
     */
    public function price(): float
    {
        return $this->price;
    }

    /**
     * @return bool
     */
    public function showStock(): bool
    {
        return $this->showStock;
    }
    
}
