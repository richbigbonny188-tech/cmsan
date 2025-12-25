<?php
/* --------------------------------------------------------------
   CustomerDetails.php 2020-08-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Withdrawal\Model\ValueObjects;

use Webmozart\Assert\Assert;

/**
 * Class CustomerDetails
 *
 * @package Gambio\Admin\Modules\Withdrawal\Model\ValueObjects
 */
class CustomerDetails
{
    /**
     * @var CustomerId
     */
    private $id;
    
    /**
     * @var CustomerGender
     */
    private $gender;
    
    /**
     * @var string
     */
    private $firstName;
    
    /**
     * @var string
     */
    private $lastName;
    
    /**
     * @var CustomerAddress
     */
    private $address;
    
    /**
     * @var string
     */
    private $email;
    
    
    /**
     * CustomerDetails constructor.
     *
     * @param CustomerId      $id
     * @param CustomerGender  $gender
     * @param string          $firstname
     * @param string          $lastname
     * @param CustomerAddress $address
     * @param string          $email
     */
    private function __construct(
        CustomerId $id,
        CustomerGender $gender,
        string $firstname,
        string $lastname,
        CustomerAddress $address,
        string $email
    ) {
        $this->id        = $id;
        $this->gender    = $gender;
        $this->firstName = $firstname;
        $this->lastName  = $lastname;
        $this->address   = $address;
        $this->email     = $email;
    }
    
    
    /**
     * @param CustomerId      $id
     * @param CustomerGender  $gender
     * @param string          $firstname
     * @param string          $lastname
     * @param CustomerAddress $address
     * @param string          $email
     *
     * @return CustomerDetails
     */
    public static function create(
        CustomerId $id,
        CustomerGender $gender,
        string $firstname,
        string $lastname,
        CustomerAddress $address,
        string $email
    ): CustomerDetails {
        Assert::email($email, 'Given email is not a valid email address. Got: %s');
        
        return new self($id, $gender, $firstname, $lastname, $address, $email);
    }
    
    
    /**
     * @return int|null
     */
    public function id(): ?int
    {
        return $this->id->value();
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
    public function street(): string
    {
        return $this->address->street();
    }
    
    
    /**
     * @return string
     */
    public function postcode(): string
    {
        return $this->address->postcode();
    }
    
    
    /**
     * @return string
     */
    public function city(): string
    {
        return $this->address->city();
    }
    
    
    /**
     * @return string
     */
    public function country(): string
    {
        return $this->address->country();
    }
    
    
    /**
     * @return string
     */
    public function email(): string
    {
        return $this->email;
    }
    
    
    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id'        => $this->id(),
            'gender'    => $this->gender(),
            'firstName' => $this->firstName(),
            'lastName'  => $this->lastName(),
            'address'   => $this->address->toArray(),
            'email'     => $this->email(),
        ];
    }
}