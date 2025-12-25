<?php
/* --------------------------------------------------------------
   InfoElementContent.inc.php 2022-08-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class InfoElementContent
 *
 * This class represents an info element content
 *
 * @category   System
 * @package    Content
 */
class InfoElementContent
    implements ContentInterface, ContentAttributesInterface, ContentHeadingAttributeInterface, ContentTextAttributeInterface, ContentGroupIdInterface, JsonSerializable
{
    /**
     * Content type
     *
     * @var string
     */
    protected $type;
    
    /**
     * Content position
     *
     * @var string
     */
    protected $position;
    
    /**
     * Content status
     *
     * @var ContentStatusCollection
     */
    protected $status;
    
    /**
     * Localized content titles
     *
     * @var ContentTitleCollection
     */
    protected $titles;
    
    /**
     * Localized content headings
     *
     * @var ContentHeadingCollection
     */
    protected $headings;
    
    /**
     * Localized content texts
     *
     * @var ContentHeadingCollection
     */
    protected $texts;
    
    /**
     * Sort order
     *
     * @var ContentSortOrder|null
     */
    protected $sortOrder;
    
    /**
     * Content group ID
     *
     * @var ContentIdentificationInterface
     */
    protected $id;
    
    
    /**
     * @param ContentPositionInterface            $elementPosition
     * @param ContentStatusCollection             $status
     * @param ContentTitleCollection              $titles
     * @param ContentHeadingCollection            $headings
     * @param ContentTextCollection               $texts
     * @param ContentIdentificationInterface|null $id
     * @param ContentSortOrder|null               $sortOrder
     */
    public function __construct(
        ContentPositionInterface $elementPosition,
        ContentStatusCollection $status,
        ContentTitleCollection $titles,
        ContentHeadingCollection $headings,
        ContentTextCollection $texts,
        ContentIdentificationInterface $id = null,
        ?ContentSortOrder $sortOrder = null
    ) {
        $this->type     = ContentType::createForContent()->type();
        $this->position = $elementPosition->position();
        $this->status   = $status;
        $this->titles   = $titles;
        $this->headings = $headings;
        $this->texts    = $texts;
        $this->id       = $id ?? null;
        $this->sortOrder = $sortOrder;
    }
    
    
    /**
     * Return the content type
     *
     * @return string
     */
    public function type(): ?string
    {
        return $this->type;
    }
    
    
    /**
     * Return the content position
     *
     * @return string
     */
    public function position(): ?string
    {
        return $this->position;
    }
    
    
    /**
     * Return whether the content is active
     *
     * @return ContentStatusCollection
     */
    public function status(): ?ContentStatusCollection
    {
        return $this->status;
    }
    
    
    /**
     * Return the localized content titles
     *
     * @return ContentTitleCollection
     */
    public function titles(): ?ContentTitleCollection
    {
        return $this->titles;
    }
    
    
    /**
     * Return the localized content headings
     *
     * @return ContentHeadingCollection
     */
    public function headings(): ?ContentHeadingCollection
    {
        return $this->headings;
    }
    
    
    /**
     * Return the localized content texts
     *
     * @return ContentTextCollection
     */
    public function texts(): ?ContentTextCollection
    {
        return $this->texts;
    }
    
    
    /**
     * Return the sort order
     *
     * @return ContentSortOrder|null
     */
    public function sortOrder(): ?ContentSortOrder
    {
        return $this->sortOrder;
    }
    
    
    /**
     * Returns the content group id if set, otherwise null will be returned.
     *
     * @return ContentIdentificationInterface
     */
    public function id() : ?ContentIdentificationInterface
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
        $result = new stdClass;
        
        $result->contentGroupId = $this->id()->contentGroup();
        $result->contentAlias = $this->id()->contentAlias();
        $result->titles         = $this->titles();
        $result->texts          = $this->texts();
        $result->sortOrder      = $this->sortOrder !== null ? $this->sortOrder->order() : 0;
        
        return $result;
    }
}
