<?php
/*--------------------------------------------------------------
   PersonalInformation.php 2022-09-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\Address\Model\ValueObjects;

/**
 * Class PersonalInformation
 *
 * @package Gambio\Admin\Modules\Customer\Submodules\Address\ValueObjects
 */
class PersonalInformation
{
    private CustomerGender $gender;
    private string         $firstName;
    private string         $lastName;
    private string         $companyName;
    
    
    /**
     * @param CustomerGender $gender
     * @param string         $firstName
     * @param string         $lastName
     * @param string         $companyName
     */
    private function __construct(
        CustomerGender $gender,
        string         $firstName,
        string         $lastName,
        string         $companyName
    ) {
        $this->gender      = $gender;
        $this->firstName   = $firstName;
        $this->lastName    = $lastName;
        $this->companyName = $companyName;
    }
    
    
    /**
     * @param CustomerGender $gender
     * @param string         $firstName
     * @param string         $lastName
     * @param string         $companyName
     *
     * @return PersonalInformation
     */
    public static function create(
        CustomerGender $gender,
        string         $firstName = '',
        string         $lastName = '',
        string         $companyName = ''
    ): PersonalInformation {
        
        return new self($gender, $firstName, $lastName, $companyName);
    }
    
    
    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'gender'      => $this->gender(),
            'firstName'   => $this->firstName(),
            'lastName'    => $this->lastName(),
            'companyName' => $this->companyName(),
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
    public function companyName(): string
    {
        return $this->companyName;
    }
}