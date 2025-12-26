<?php
/*--------------------------------------------------------------------------------------------------
    ContentIdentification.php 2022-08-05
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2022 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

/**
 * Class ContentIdentification
 */
class ContentIdentification implements JsonSerializable, ContentIdentificationInterface
{
    protected $content_group;
    protected $content_alias;
    
    
    /**
     * @return mixed
     */
    public function contentGroup() : ?int
    {
        return $this->content_group;
    }
    
    
    /**
     * @return mixed
     */
    public function contentAlias() : ?string
    {
        return $this->content_alias;
    }
    
    
    /**
     * ContentIdentification constructor.
     *
     * @param null $contentGroup
     * @param null $contentAlias
     */
    public function __construct($contentGroup = null, $contentAlias = null)
    {

        if(!isset($contentGroup) && !isset($contentAlias) ){
            throw new InvalidArgumentException('ContentGroup or contentAlias must be supplied!');
        }
        $this->content_group = $contentGroup;
        $this->content_alias = $contentAlias;
    }
    
    /**
     * @param string $themeId
     *
     * @return ContentIdentificationInterface
     */
    public function forTheme(string $themeId) : ContentIdentificationInterface {
        //raise an error here saying that the cotntentGroup is not set
        $contentGroup = $this->contentGroup();
        $alias = $themeId . '-' . $this->contentGroup();
        return new class ($contentGroup, $alias)extends ContentIdentification{};
        
    }
    
    
    /**
     * @inheritDoc
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            'alias' =>$this->content_alias,
            'groupId' => $this->content_group
        ];
    }
    
    
    /**
     * @return string
     */
    public function __toString()
    {
        return "Alias : {$this->content_alias}; ContentGroup: {$this->content_group}";
    }
}