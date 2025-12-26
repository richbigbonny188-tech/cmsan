<?php
/*--------------------------------------------------------------
   ZoneId.php 2022-07-21
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Country\Model\ValueObjects;

use Webmozart\Assert\Assert;

/**
 * Class ZoneId
 *
 * @package Gambio\Admin\Modules\Country\Model\ValueObjects
 */
class ZoneId
{
    private int $value;
    
    
    /**
     * CountryId constructor.
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
     * @return ZoneId
     */
    public static function create(int $value): ZoneId
    {
        Assert::greaterThan($value, 0, 'Given zone ID must be greater than 0. Got: %s');
        
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