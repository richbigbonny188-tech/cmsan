<?php
/*--------------------------------------------------------------
   CustomerGender.php 2022-03-03
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
 
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Model\ValueObjects;

use Webmozart\Assert\Assert;

/**
 * Class CustomerGender
 *
 * @package Gambio\Admin\Modules\Customer\Model\ValueObjects
 */
class CustomerGender
{
    public const GENDER_MALE = 'm';
    public const GENDER_FEMALE = 'f';
    public const GENDER_OTHER = 'o';
    public const GENDER_ALLOWED_VALUES = [
        self::GENDER_MALE,
        self::GENDER_FEMALE,
        self::GENDER_OTHER,
        ''
    ];
    private string $value;
    
    
    /**
     * @param string|null $value
     */
    private function __construct(string $value)
    {
        $this->value = $value;
    }
    
    
    /**
     * @param string $value
     *
     * @return CustomerGender
     */
    public static function create(string $value): CustomerGender
    {
        Assert::oneOf($value,
                      self::GENDER_ALLOWED_VALUES,
                      'Invalid gender given. Need to be one of: ' . implode(', ', self::GENDER_ALLOWED_VALUES) . '; Got: %s');
        
        return new self($value);
    }
    
    /**
     * @return string
     */
    public function value(): string
    {
        return $this->value;
    }
}