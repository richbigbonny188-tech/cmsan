<?php
/*--------------------------------------------------------------------------------------------------
    AttributeInfo.php 2020-3-19
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2016 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */
namespace Gambio\Shop\Attributes\SellingUnitQuantitiy\Database\Repository\DTO;


class AttributeInfo
{
    /**
     * @var float
     */
    protected $value;

    /**
     * @return float
     */
    public function value(): float
    {
        return $this->value;
    }

    /**
     * AttributeInfo constructor.
     * @param float $value

     */
    public function __construct(float $value)
    {
        $this->value = $value;
    }


}