<?php
/* --------------------------------------------------------------
   HermesHSIName.inc.php 2022-08-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

class HermesHSIName implements JsonSerializable
{
    /** @var string */
    protected $title;
    
    /** @var string */
    protected $gender;
    
    /** @var string */
    protected $firstname;
    
    /** @var string */
    protected $middlename;
    
    /** @var string */
    protected $lastname;
    
    
    /**
     * HermesHSIName constructor.
     *
     * @param NonEmptyStringType $lastname
     *
     * @throws HermesHSIInvalidDataException
     */
    public function __construct(NonEmptyStringType $lastname)
    {
        $this->title      = '';
        $this->gender     = 'O';
        $this->firstname  = '';
        $this->middlename = '';
        $this->lastname   = '';
        $this->setLastname($lastname->asString());
    }
    
    
    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }
    
    
    /**
     * @param string $title
     *
     * @throws HermesHSIInvalidDataException
     */
    public function setTitle(string $title): void
    {
        if (mb_strlen($title) > 20) {
            throw new HermesHSIInvalidDataException('title cannot be longer than 20 characters');
        }
        $this->title = $title;
    }
    
    
    /**
     * @return string
     */
    public function getGender(): string
    {
        return $this->gender;
    }
    
    
    /**
     * @param string $gender
     *
     * @throws HermesHSIInvalidDataException
     */
    public function setGender(string $gender): void
    {
        $gender = strtoupper($gender);
        if ($gender === 'F') {
            $gender = 'W';
        }
        if (!in_array($gender, ['M', 'W', 'O'], true)) {
            throw new HermesHSIInvalidDataException('gender must be M, W or O');
        }
        $this->gender = $gender;
    }
    
    
    /**
     * @return string
     */
    public function getFirstname(): string
    {
        return $this->firstname;
    }
    
    
    /**
     * @param string $firstname
     *
     * @throws HermesHSIInvalidDataException
     */
    public function setFirstname(string $firstname): void
    {
        if (mb_strlen($firstname) > 20) {
            throw new HermesHSIInvalidDataException('firstname cannot be longer than 20 characters');
        }
        $this->firstname = $firstname;
    }
    
    
    /**
     * @return string
     */
    public function getMiddlename(): string
    {
        return $this->middlename;
    }
    
    
    /**
     * @param string $middlename
     *
     * @throws HermesHSIInvalidDataException
     */
    public function setMiddlename(string $middlename): void
    {
        if (mb_strlen($middlename) > 30) {
            throw new HermesHSIInvalidDataException('middlename cannot be longer than 30 characters');
        }
        $this->middlename = $middlename;
    }
    
    
    /**
     * @return string
     */
    public function getLastname(): string
    {
        return $this->lastname;
    }
    
    
    /**
     * @param string $lastname
     *
     * @throws HermesHSIInvalidDataException
     */
    public function setLastname(string $lastname): void
    {
        if (mb_strlen($lastname) > 30) {
            throw new HermesHSIInvalidDataException('title cannot be longer than 30 characters');
        }
        $this->lastname = $lastname;
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
        return [
            'title'      => $this->title,
            'gender'     => $this->gender,
            'firstname'  => $this->firstname,
            'middlename' => $this->middlename,
            'lastname'   => $this->lastname,
        ];
    }
}
