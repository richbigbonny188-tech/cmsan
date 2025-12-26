<?php
/*--------------------------------------------------------------
   PersonalInformation.php 2021-12-17
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
 
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Model\ValueObjects;

use DateTimeImmutable;

/**
 * Class PersonalInformation
 *
 * @package Gambio\Admin\Modules\Customer\Model\ValueObjects
 */
class PersonalInformation
{
    private CustomerGender     $gender;
    private string             $firstName;
    private string             $lastName;
    private string             $customerNumber;
    private ?DateTimeImmutable $dateOfBirth;
    
    
    /**
     * @param CustomerGender         $gender
     * @param string                 $firstName
     * @param string                 $lastName
     * @param string                 $customerNumber
     * @param DateTimeImmutable|null $dateOfBirth
     */
    private function __construct(
        CustomerGender     $gender,
        string             $firstName,
        string             $lastName,
        string             $customerNumber,
        ?DateTimeImmutable $dateOfBirth
    ) {
        $this->gender         = $gender;
        $this->firstName      = $firstName;
        $this->lastName       = $lastName;
        $this->customerNumber = $customerNumber;
        $this->dateOfBirth    = $dateOfBirth;
    }
    
    
    /**
     * @param CustomerGender         $gender
     * @param string                 $firstName
     * @param string                 $lastName
     * @param string                 $customerNumber
     * @param DateTimeImmutable|null $dateOfBirth
     *
     * @return PersonalInformation
     */
    public static function create(
        CustomerGender     $gender,
        string             $firstName = '',
        string             $lastName = '',
        string             $customerNumber = '',
        ?DateTimeImmutable $dateOfBirth = null
    ): PersonalInformation {
        
        return new self ($gender, $firstName, $lastName, $customerNumber, $dateOfBirth);
    }
    
    
    /**
     * @param string $datetimeFormat
     *
     * @return string[]
     */
    public function toArray(string $datetimeFormat = 'Y-m-d H:i:s'): array
    {
        return [
            "gender"         => $this->gender(),
            "firstName"      => $this->firstName(),
            "lastName"       => $this->lastName(),
            "dateOfBirth"    => $this->dateOfBirth($datetimeFormat),
            "customerNumber" => $this->customerNumber(),
        ];
    }
    
    
    /**
     * @return string
     */
    public function gender(): string
    {
        return $this->gender->value();
    }
    
    
    /**
     * @return string
     */
    public function firstName(): string
    {
        return $this->firstName;
    }
    
    
    /**
     * @return string
     */
    public function lastName(): string
    {
        return $this->lastName;
    }
    
    
    /**
     * @return string
     */
    public function customerNumber(): string
    {
        return $this->customerNumber;
    }
    
    
    /**
     * @param string $datetimeFormat
     *
     * @return string|null
     */
    public function dateOfBirth(string $datetimeFormat = 'Y-m-d H:i:s'): ?string
    {
        return $this->dateOfBirth !== null ? $this->dateOfBirth->format($datetimeFormat) : null;
    }
}