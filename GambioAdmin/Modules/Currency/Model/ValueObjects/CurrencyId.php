<?php
/*--------------------------------------------------------------
   CurrencyId.php 2022-06-08
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Currency\Model\ValueObjects;

use Webmozart\Assert\Assert;

/**
 * Class CurrencyId
 *
 * @package Gambio\Admin\Modules\Currency\Model\ValueObjects
 */
class CurrencyId
{
    /**
     * @var int
     */
    private $value;
    
    
    /**
     * CurrencyId constructor.
     *
     * @param int $value
     */
    private function __construct(int $value)
    {
        $this->value = $value;
    }
    
    
    /**
     * @param int $value
     *
     * @return CurrencyId
     */
    public static function create(int $value): CurrencyId
    {
        Assert::greaterThan($value, 0, 'Given currency ID must be greater than 0. Got: %s');
        
        return new self($value);
    }
    
    
    /**
     * @return int
     */
    public function value(): int
    {
        return $this->value;
    }
}