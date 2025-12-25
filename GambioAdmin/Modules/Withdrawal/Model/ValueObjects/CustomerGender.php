<?php
/* --------------------------------------------------------------
   CustomerGender.php 2022-02-04
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Withdrawal\Model\ValueObjects;

use Webmozart\Assert\Assert;

/**
 * Class CustomerGender
 *
 * @package Gambio\Admin\Modules\Withdrawal\Model\ValueObjects
 */
class CustomerGender
{
    public const MALE    = 'm';
    public const FEMALE  = 'f';
    public const OTHER   = 'o';
    public const DIVERSE = 'o';
    
    /**
     * @var string
     */
    private $value;
    
    
    /**
     * CustomerGender constructor.
     *
     * @param string $value
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
    public static function create(?string $value): CustomerGender
    {
        $value = trim($value ?? '');
        $validValues = [
            self::MALE,
            self::FEMALE,
            self::OTHER,
            self::DIVERSE,
            '',
        ];
        
        Assert::oneOf($value,
                      $validValues,
                      'Gender needs to be one of: ' . implode(', ', $validValues) . '; Got: %s');
        
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