<?php

/* --------------------------------------------------------------
   StaticSeoUrlContent.inc.php 2017-05-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class StaticSeoUrlContent
 *
 * Represents the default settings of a single StaticSeoUrlContent in a StaticSeoUrl.
 *
 * @category   System
 * @package    StaticSeoUrl
 * @subpackage Entities
 */
class StaticSeoUrlContent implements StaticSeoUrlContentInterface
{
    /**
     * StaticSeoUrlContent ID
     *
     * @var int
     */
    protected $id = 0;
    
    /**
     * Language ID
     *
     * @var int
     */
    protected $languageId = 0;
    
    /**
     * Title
     *
     * @var string
     */
    protected $title = '';
    
    /**
     * Meta Description
     *
     * @var string
     */
    protected $description = '';
    
    /**
     * Meta Keywords
     *
     * @var string
     */
    protected $keywords = '';
    
    
    /**
     * Set the staticSeoUrlContent ID for the staticSeoUrlContent.
     *
     * @param IdType $id StaticSeoUrlContent ID.
     *
     * @return StaticSeoUrlContentInterface Same instance for chained method calls.
     */
    public function setId(IdType $id)
    {
        $this->id = $id->asInt();
        
        return $this;
    }
    
    
    /**
     * Return the staticSeoUrlContent ID.
     *
     * @return int StaticSeoUrlContent ID.
     */
    public function getId()
    {
        return $this->id;
    }
    
    
    /**
     * Set the language ID for the staticSeoUrlContent.
     *
     * @param IdType $languageId Language ID.
     *
     * @return StaticSeoUrlContentInterface Same instance for chained method calls.
     */
    public function setLanguageId(IdType $languageId)
    {
        $this->languageId = $languageId->asInt();
        
        return $this;
    }
    
    
    /**
     * Return the language ID.
     *
     * @return int Language ID.
     */
    public function getLanguageId()
    {
        return $this->languageId;
    }
    
    
    /**
     * Set the title for the staticSeoUrlContent.
     *
     * @param StringType $title Title for the staticSeoUrlContent.
     *
     * @return StaticSeoUrlContentInterface Same instance for chained method calls.
     */
    public function setTitle(StringType $title)
    {
        $this->title = $title->asString();
        
        return $this;
    }
    
    
    /**
     * Return the title of the staticSeoUrlContent.
     *
     * @return string Title.
     */
    public function getTitle()
    {
        return $this->title;
    }
    
    
    /**
     * Set the description for the staticSeoUrlContent.
     *
     * @param StringType $description Description for the staticSeoUrlContent.
     *
     * @return StaticSeoUrlContentInterface Same instance for chained method calls.
     */
    public function setDescription(StringType $description)
    {
        $this->description = $description->asString();
        
        return $this;
    }
    
    
    /**
     * Return the description of the staticSeoUrlContent.
     *
     * @return string Description.
     */
    public function getDescription()
    {
        return $this->description;
    }
    
    
    /**
     * Set the keywords for the staticSeoUrlContent.
     *
     * @param StringType $keywords Keywords for the staticSeoUrlContent.
     *
     * @return StaticSeoUrlContentInterface Same instance for chained method calls.
     */
    public function setKeywords(StringType $keywords)
    {
        $this->keywords = $keywords->asString();
        
        return $this;
    }
    
    
    /**
     * Return the keywords of the staticSeoUrlContent.
     *
     * @return string Keywords.
     */
    public function getKeywords()
    {
        return $this->keywords;
    }
}