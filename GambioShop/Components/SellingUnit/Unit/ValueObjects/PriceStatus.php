<?php
/*--------------------------------------------------------------------------------------------------
    PriceStatus.php 2020-3-9
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2016 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */
namespace Gambio\Shop\SellingUnit\Unit\ValueObjects;


class PriceStatus
{

    /**
     * @var int
     */
    protected $value;

    /**
     * PriceStatus constructor.
     * @param int $value
     */
    public function __construct(int $value)
    {
        $this->value = $value;
    }

    /**
     * @return int
     */
    public function value(): int
    {
        return $this->value;
    }

    /**
     * @param PriceStatus $status
     * @return bool
     */
    public function equals(PriceStatus $status): bool
    {
        return $status && $this->value === $status->value();
    }

    /**
     * @param int $statusValue
     * @return bool
     */
    public function equalsInt(int $statusValue): bool
    {
        return $this->value === $statusValue;
    }
}