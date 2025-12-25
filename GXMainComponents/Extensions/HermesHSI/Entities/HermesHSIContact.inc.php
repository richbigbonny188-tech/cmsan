<?php
/* --------------------------------------------------------------
   HermesHSIContact.inc.php 2022-08-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

class HermesHSIContact implements JsonSerializable
{
    /** @var string */
    protected $mail;
    
    /** @var string */
    protected $mobile;
    
    /** @var string */
    protected $phone;
    
    
    public function __construct()
    {
        $this->mail   = '';
        $this->mobile = '';
        $this->phone  = '';
    }
    
    
    /**
     * @return string
     */
    public function getMail(): string
    {
        return $this->mail;
    }
    
    
    /**
     * @param string $mail
     *
     * @throws HermesHSIInvalidDataException
     */
    public function setMail(string $mail): void
    {
        if (mb_strlen($mail) > 200) {
            throw new HermesHSIInvalidDataException('maximum length for mail is 200 characters');
        }
        $this->mail = $mail;
    }
    
    
    /**
     * @return string
     */
    public function getMobile(): string
    {
        return $this->mobile;
    }
    
    
    /**
     * @param string $mobile
     *
     * @throws HermesHSIInvalidDataException
     */
    public function setMobile(string $mobile): void
    {
        if (mb_strlen($mobile) > 20) {
            throw new HermesHSIInvalidDataException('maximum length for mobile is 20 characters');
        }
        $this->mobile = $mobile;
    }
    
    
    /**
     * @return string
     */
    public function getPhone(): string
    {
        return $this->phone;
    }
    
    
    /**
     * @param string $phone
     *
     * @throws HermesHSIInvalidDataException
     */
    public function setPhone(string $phone): void
    {
        if (mb_strlen($phone) > 20) {
            throw new HermesHSIInvalidDataException('maximum length for phone is 20 characters');
        }
        $this->phone = $phone;
    }
    
    
    /**
     * Specify data which should be serialized to JSON
     * @link  https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        $jsonData = array_filter([
                                'mail'   => $this->mail,
                                'mobile' => $this->mobile,
                                'phone'  => $this->phone,
                            ]);
        $jsonData = empty($jsonData) ? null : $jsonData;
        return $jsonData;
    }
}
