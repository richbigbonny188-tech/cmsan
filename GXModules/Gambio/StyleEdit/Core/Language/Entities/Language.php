<?php
/*--------------------------------------------------------------------------------------------------
    Language.php 2022-08-05
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2022 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\StyleEdit\Core\Language\Entities;

/**
 * Class Language
 * @package Gambio\StyleEdit\Core
 */
class Language implements \JsonSerializable
{
    /**
     * @var string
     */
    protected $code = 'en';
    
    protected $id = 0;
    
    
    /**
     * Language constructor.
     *
     * @param string $code
     * @param int    $id
     */
    public function __construct(string $code, int $id)
    {
        $this->code = strtolower($code);
        $this->id   = $id;
    }
    
    
    /**
     * Return the current language.
     *
     * @return string
     */
    public function code(): string
    {
        return $this->code;
    }
    
    
    /**
     * @return int
     */
    public function id(): int
    {
        return $this->id;
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
        return $this->code();
    }
}