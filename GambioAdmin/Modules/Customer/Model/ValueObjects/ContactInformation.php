<?php
/*--------------------------------------------------------------
   ContactInformation.php 2022-07-25
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
 
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Model\ValueObjects;

use Gambio\Admin\Modules\Customer\Services\Exceptions\EmailAddressIsInvalidException;

/**
 * Class ContactInformation
 *
 * @package Gambio\Admin\Modules\Customer\Model\ValueObjects
 */
class ContactInformation
{
    private string $email;
    private string $phoneNumber;
    private string $faxNumber;
    
    
    /**
     * @param string $email
     * @param string $phoneNumber
     * @param string $faxNumber
     */
    private function __construct(string $email, string $phoneNumber, string $faxNumber)
    {
        $this->email       = $email;
        $this->phoneNumber = $phoneNumber;
        $this->faxNumber   = $faxNumber;
    }
    
    
    /**
     * @param string $email
     * @param string $phoneNumber
     * @param string $faxNumber
     *
     * @return ContactInformation
     * @throws EmailAddressIsInvalidException
     */
    public static function create(string $email, string $phoneNumber = '', string $faxNumber = ''): ContactInformation
    {
        if ((bool)preg_match('#.+@.+\..+#', $email) === false
            || substr_count($email, '@') !== 1) {
        
            throw EmailAddressIsInvalidException::forEmail($email);
        }
        
        return new self($email, $phoneNumber, $faxNumber);
    }
    
    
    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            "email"       => $this->email(),
            "phoneNumber" => $this->phoneNumber(),
            "faxNumber"   => $this->faxNumber(),
        ];
    }
    
    
    /**
     * @return string
     */
    public function email(): string
    {
        return $this->email;
    }
    
    
    /**
     * @return string
     */
    public function phoneNumber(): string
    {
        return $this->phoneNumber;
    }
    
    
    /**
     * @return string
     */
    public function faxNumber(): string
    {
        return $this->faxNumber;
    }
}