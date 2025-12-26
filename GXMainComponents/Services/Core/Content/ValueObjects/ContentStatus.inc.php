<?php
/* --------------------------------------------------------------
   ContentStatus.inc.php 2022-08-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ContentStatus
 *
 * This class represents the content active status
 *
 * @category   System
 * @package    Content
 */
class ContentStatus implements LocalizedContentAttributeInterface, JsonSerializable
{
    /**
     * Content active status
     *
     * @var string
     */
    protected $status;
    
    
    /**
     * @var LanguageCode
     */
    protected $languageCode;
    
    
    /**
     * ContentStatus constructor
     *
     * @param string $status Content status
     */
    public function __construct(string $status, LanguageCode $languageCode)
    {
        $this->status       = $status;
        $this->languageCode = $languageCode->asString();
    }
    
    
    /**
     * @return string
     */
    public function content(): string
    {
        return $this->status;
    }
    
    
    public function languageCode(): string
    {
        return $this->languageCode;
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
        return $this->content();
    }
}
