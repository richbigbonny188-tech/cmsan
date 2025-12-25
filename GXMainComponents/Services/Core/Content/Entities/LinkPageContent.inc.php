<?php
/* --------------------------------------------------------------
   LinkPageContent.inc.php 2021-07-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class LinkPageContent
 *
 * This class represents a link page content entity
 *
 * @category   System
 * @package    Content
 */
class LinkPageContent
    implements ContentInterface, ContentAttributesInterface, ContentNameAttributeInterface, ContentLinkAttributesInterface, ContentGroupIdInterface, ContentDeleteInterface, ContentSortOrderInterface
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
     * Content is active
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
     * Localized content names
     *
     * @var ContentNameCollection
     */
    protected $names;
    
    /**
     * Localized content links
     *
     * @var ContentLinkCollection
     */
    protected $links;
    
    /**
     * content is opening in new tab
     *
     * @var bool
     */
    protected $opensInNewTabStatus;
    
    /**
     * Content group ID
     *
     * @var ContentIdentificationInterface
     */
    protected $id;
    
    /**
     * Content sort order
     *
     * @var int
     */
    protected $sortOrder;
    
    
    /**
     * @var bool
     */
    protected $deletable;
    
    
    /**
     * LinkPageContent constructor
     *
     * @param ContentPositionInterface            $pagePosition        Page position
     * @param ContentStatusCollection             $status              Localized status
     * @param ContentTitleCollection              $titles              Localized titles
     * @param ContentNameCollection               $names               Localized names
     * @param ContentLinkCollection               $links               Localized links
     * @param ContentOpenInNewTabStatusCollection $opensInNewTabStatus Whether the link is opened in a new tab
     * @param ContentIdentificationInterface|null $id                  Optional content group ID
     * @param ContentSortOrderInterface|null      $sortOrder
     * @param ContentDeleteInterface|null         $deletable
     */
    public function __construct(
        ContentPositionInterface $pagePosition,
        ContentStatusCollection $status,
        ContentTitleCollection $titles,
        ContentNameCollection $names,
        ContentLinkCollection $links,
        ContentOpenInNewTabStatusCollection $opensInNewTabStatus,
        ContentIdentificationInterface $id = null,
        ContentSortOrderInterface $sortOrder = null,
        ContentDeleteInterface $deletable = null
    ) {
        $this->type                = ContentType::createForLink()->type();
        $this->position            = $pagePosition->position();
        $this->status              = $status;
        $this->titles              = $titles;
        $this->names               = $names;
        $this->links               = $links;
        $this->opensInNewTabStatus = $opensInNewTabStatus;
        $this->id                  = $id ?? null;
        $this->sortOrder           = $sortOrder ? $sortOrder->order() : 0;
        $this->deletable           = $deletable && $deletable->isDeletable();
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
     * Return the localized content names
     *
     * @return ContentNameCollection
     */
    public function names(): ?ContentNameCollection
    {
        return $this->names;
    }
    
    
    /**
     * Return the localized content links
     *
     * @return ContentLinkCollection
     */
    public function links(): ?ContentLinkCollection
    {
        return $this->links;
    }
    
    
    /**
     * Return localized content is opened in a new tab
     *
     * @return ContentOpenInNewTabStatusCollection
     */
    public function openInNewTab(): ?ContentOpenInNewTabStatusCollection
    {
        return $this->opensInNewTabStatus;
    }
    
    
    /**
     * Returns the content group id if set, otherwise null will be returned.
     *
     * @return ContentIdentificationInterface|null
     */
    public function id() : ?ContentIdentificationInterface
    {
        return $this->id;
    }
    
    
    /**
     * Returns the content group id if set, otherwise null will be returned.
     *
     * @return int
     */
    public function order(): int
    {
        return $this->sortOrder;
    }
    
    
    /**
     * Returns whether the content is deletable
     *
     * @return int|null
     */
    public function isDeletable(): bool
    {
        return $this->deletable;
    }
}
