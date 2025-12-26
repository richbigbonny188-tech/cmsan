<?php
/* --------------------------------------------------------------
   LinkPageContentBuilder.inc.php 2022-10-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class LinkPageContentBuilder
 *
 * This class represents a link page content builder
 *
 * @category   System
 * @package    Content
 */
class LinkPageContentBuilder implements BuilderInterface, LinkPageContentBuilderInterface
{
    /**
     * Content position
     *
     * @var PagePosition
     */
    protected $position;
    
    /**
     * Content status
     *
     * @var ContentStatus
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
     * Whether the link page is opened in a new tab
     *
     * @var ContentOpenInNewTabStatus
     */
    protected $openInNewTabStatus;
    
    /**
     * Content group id
     *
     * @var int|null
     */
    protected $id;
    
    /**
     * Content sort order
     *
     * @var ContentSortOrderInterface
     */
    protected $sortOrder;
    
    /**
     * Content is deletable
     *
     * @var ContentDeleteInterface
     */
    protected $deletable;
    
    
    /**
     * Return new instance of the builder
     *
     * @return LinkPageContentBuilder New instance
     */
    public static function create(): self
    {
        return new self;
    }
    
    
    /**
     * Return the created LinkPageContent instance
     *
     * @return LinkPageContent Link page content entity object
     *
     * @throws UnfinishedBuildException On calling build() before having all properties set
     */
    public function build(): LinkPageContent
    {
        $properties = [
            'position'           => $this->position,
            'status'             => $this->status,
            'titles'             => $this->titles,
            'names'              => $this->names,
            'links'              => $this->links,
            'openInNewTabStatus' => $this->openInNewTabStatus,
            'id'                 => $this->id,
            'sortOrder'          => $this->sortOrder,
            'deletable'          => $this->deletable,
        ];
        
         $requiredProperties = ['position', 'status', 'titles', 'names', 'links', 'openInNewTabStatus'];
        
        foreach ($requiredProperties as $name) {
            
            if ($properties[$name] === null)  {
    
                throw new UnfinishedBuildException("Property {$name} is not set");
            }
        }
        
        return new LinkPageContent(...array_values($properties));
    }
    
    
    /**
     * Set the page position
     *
     * @param PagePosition $position Page position
     *
     * @return LinkPageContentBuilder Same instance
     */
    public function inPosition(PagePosition $position): self
    {
        $this->position = $position;
        
        return $this;
    }
    
    
    /**
     * Set the activation status
     *
     * @param ContentStatusCollection $status Activation status
     *
     * @return LinkPageContentBuilder Same instance
     */
    public function usingStatus(ContentStatusCollection $status): self
    {
        $this->status = $status;
        
        return $this;
    }
    
    
    /**
     * Set the localized content titles
     *
     * @param ContentTitleCollection $titles Localized titles
     *
     * @return LinkPageContentBuilder Same instance
     */
    public function usingTitles(ContentTitleCollection $titles): self
    {
        $this->titles = $titles;
        
        return $this;
    }
    
    
    /**
     * Set the localized content names
     *
     * @param ContentNameCollection $names Localized names
     *
     * @return LinkPageContentBuilder Same instance
     */
    public function usingNames(ContentNameCollection $names): self
    {
        $this->names = $names;
        
        return $this;
    }
    
    
    /**
     * Set the localized content links
     *
     * @param ContentLinkCollection $links Localized links
     *
     * @return LinkPageContentBuilder Same instance
     */
    public function usingLinks(ContentLinkCollection $links): self
    {
        $this->links = $links;
        
        return $this;
    }
    
    
    /**
     * Set whether the link page opens in a new tab
     *
     * @param ContentOpenInNewTabStatusCollection $openInNewTabStatus Whether the link page opens in a new tab
     *
     * @return LinkPageContentBuilder Same instance
     */
    public function usingOpenInNewTabStatus(ContentOpenInNewTabStatusCollection $openInNewTabStatus): self
    {
        $this->openInNewTabStatus = $openInNewTabStatus;
        
        return $this;
    }
    
    
    /**
     * Set the content group id
     *
     * @param ContentIdentificationInterface|null $id
     *
     * @return LinkPageContentBuilder Same instance
     */
    public function usingId(ContentIdentificationInterface $id = null): LinkPageContentBuilder
    {
        $this->id = $id;
        
        return $this;
    }
    
    
    /**
     * Set the content sort order
     *
     * @param ContentSortOrderInterface|null $sortOrder
     *
     * @return LinkPageContentBuilder Same instance
     */
    public function usingSortOrder(ContentSortOrderInterface $sortOrder = null): LinkPageContentBuilder
    {
        $this->sortOrder = $sortOrder;
        
        return $this;
    }
    
    
    /**
     * Set the content sort order
     *
     * @param ContentDeleteInterface |null $deletable
     *
     * @return LinkPageContentBuilder Same instance
     */
    public function usingDelete(ContentDeleteInterface $deletable = null): LinkPageContentBuilder
    {
        $this->deletable = $deletable;
        
        return $this;
    }
}
