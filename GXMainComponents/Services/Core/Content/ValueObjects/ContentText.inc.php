<?php
/* --------------------------------------------------------------
   ContentText.inc.php 2022-08-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ContentText
 *
 * This class represents the localized content text
 *
 * @category   System
 * @package    Content
 */
class ContentText implements LocalizedContentAttributeInterface, JsonSerializable
{
    /**
     * Content text
     *
     * @var string
     */
    protected $text;
    
    /**
     * Language code
     *
     * @var string
     */
    protected $languageCode;
    
    
    /**
     * ContentText constructor
     *
     * @param string       $text         Content text
     * @param LanguageCode $languageCode Language code
     */
    public function __construct(string $text, LanguageCode $languageCode)
    {
        $this->text         = $text;
        $this->languageCode = $languageCode->asString();
    }
    
    
    /**
     * Return the content text
     *
     * @return string
     */
    public function content(): string
    {
        return $this->text;
    }
    
    
    /**
     * Return the language code
     *
     * @return string
     */
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