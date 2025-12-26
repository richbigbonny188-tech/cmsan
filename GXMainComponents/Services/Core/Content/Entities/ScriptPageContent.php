<?php
/*--------------------------------------------------------------------------------------------------
    ScriptPageContent.php 2023-03-06
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2023 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

declare(strict_types=1);


/**
 * Class ScriptPageContent
 */
class ScriptPageContent
    implements ContentInterface, ContentAttributesInterface, ContentScriptFileAttributeInterface, ContentMetaAttributesInterface, ContentNameAttributeInterface, ContentGroupIdInterface, ContentSortOrderInterface, ContentDeleteInterface
{
    /**
     * @var ContentNameCollection
     */
    protected $names;
    
    /**
     * @var ContentTitleCollection
     */
    protected $titles;
    
    /**
     * @var ContentPositionInterface
     */
    protected $position;
    
    /**
     * @var ContentIdentificationInterface
     */
    protected $id;
    
    /**
     * @var ContentStatusCollection
     */
    protected $status;
    
    /**
     * @var ContentAllowRobotsStatusInterface
     */
    protected $allowRobotsStatuses;
    
    /**
     * @var ContentMetaDescriptionCollection
     */
    protected $metaDescriptions;
    
    /**
     * @var ContentMetaKeywordsCollection
     */
    protected $metaKeywords;
    
    /**
     * @var ContentMetaTitleCollection
     */
    protected $metaTitles;
    
    /**
     * @var ContentUrlKeywordsCollection
     */
    protected $urlKeywords;
    
    /**
     * @var ContentUrlRewriteCollection
     */
    protected $urlRewrites;
    
    /**
     * @var ContentSitemapInterface
     */
    protected $sitemaps;
    
    /**
     * @var ContentSortOrderInterface
     */
    protected $sortOrder;
    
    /**
     * @var ContentDeleteInterface
     */
    protected $deletable;
    
    /**
     * @var ContentOpenInNewTabStatusCollection
     */
    protected $openInNewTab;
    
    /**
     * @var ContentScriptFileCollection
     */
    protected $scriptFiles;
    
    /**
     * Content type
     *
     * @var string
     */
    protected $type;
    
    
    /**
     * @param ContentNameCollection                    $names
     * @param ContentTitleCollection                   $titles
     * @param ContentPositionInterface                 $position
     * @param ContentStatusCollection                  $status
     * @param ContentScriptFileCollection              $scriptFiles
     * @param ContentIdentificationInterface|null      $id
     * @param ContentAllowRobotsStatusCollection|null  $allowRobotsStatuses
     * @param ContentMetaDescriptionCollection|null    $metaDescriptions
     * @param ContentMetaKeywordsCollection|null       $metaKeywords
     * @param ContentMetaTitleCollection|null          $metaTitles
     * @param ContentUrlKeywordsCollection|null        $urlKeywords
     * @param ContentUrlRewriteCollection|null         $urlRewrites
     * @param ContentSitemapCollection|null            $sitemaps
     * @param ContentSortOrderInterface|null           $sortOrder
     * @param ContentDeleteInterface|null              $deletable
     * @param ContentOpenInNewTabStatusCollection|null $openInNewTab
     */
    public function __construct(
        ContentNameCollection               $names,
        ContentTitleCollection              $titles,
        ContentPositionInterface            $position,
        ContentStatusCollection             $status,
        ContentScriptFileCollection         $scriptFiles,
        ContentIdentificationInterface      $id = null,
        ContentAllowRobotsStatusCollection  $allowRobotsStatuses = null,
        ContentMetaDescriptionCollection    $metaDescriptions = null,
        ContentMetaKeywordsCollection       $metaKeywords = null,
        ContentMetaTitleCollection          $metaTitles = null,
        ContentUrlKeywordsCollection        $urlKeywords = null,
        ContentUrlRewriteCollection         $urlRewrites = null,
        ContentSitemapCollection            $sitemaps = null,
        ContentSortOrderInterface           $sortOrder = null,
        ContentDeleteInterface              $deletable = null,
        ContentOpenInNewTabStatusCollection $openInNewTab = null
    ) {
        // required
        $this->names            = $names;
        $this->titles           = $titles;
        $this->position         = $position->position();
        $this->status           = $status;
        $this->scriptFiles      = $scriptFiles;
    
        //not required
        $this->id                  = $id;
        $this->allowRobotsStatuses = $allowRobotsStatuses;
        $this->metaDescriptions    = $metaDescriptions;
        $this->metaKeywords     = $metaKeywords;
        $this->metaTitles       = $metaTitles;
        $this->urlKeywords      = $urlKeywords;
        $this->urlRewrites      = $urlRewrites;
        $this->sitemaps         = $sitemaps;
        $this->sortOrder        = !is_null($sortOrder) ? $sortOrder->order() : null;
        $this->deletable        = $deletable && $deletable->isDeletable();
        $this->openInNewTab     = $openInNewTab;
    
        $this->type = ContentType::createForFile()->type();
    }
    
    
    /**
     * @return ContentScriptFileCollection|null
     */
    public function scripts(): ?ContentScriptFileCollection
    {
        return $this->scriptFiles;
    }
    
    
    /**
     * @return string|null
     */
    public function type(): ?string
    {
        return $this->type;
    }
    
    
    /**
     * @return string|null
     */
    public function position(): ?string
    {
        return $this->position;
    }
    
    
    /**
     * @return ContentStatusCollection|null
     */
    public function status(): ?ContentStatusCollection
    {
        return $this->status;
    }
    
    
    /**
     * @return ContentTitleCollection|null
     */
    public function titles(): ?ContentTitleCollection
    {
        return $this->titles;
    }
    
    
    /**
     * @return ContentMetaTitleCollection|null
     */
    public function metaTitles(): ?ContentMetaTitleCollection
    {
        return $this->metaTitles;
    }
    
    
    /**
     * @return ContentMetaKeywordsCollection|null
     */
    public function metaKeywords(): ?ContentMetaKeywordsCollection
    {
        return $this->metaKeywords;
    }
    
    
    /**
     * @return ContentMetaDescriptionCollection|null
     */
    public function metaDescriptions(): ?ContentMetaDescriptionCollection
    {
        return $this->metaDescriptions;
    }
    
    
    /**
     * @return ContentUrlKeywordsCollection|null
     */
    public function urlKeywords(): ?ContentUrlKeywordsCollection
    {
        return $this->urlKeywords;
    }
    
    
    /**
     * @return ContentUrlRewriteCollection|null
     */
    public function urlRewrites(): ?ContentUrlRewriteCollection
    {
        return $this->urlRewrites;
    }
    
    
    /**
     * @return ContentSitemapCollection|null
     */
    public function sitemaps(): ?ContentSitemapCollection
    {
        return $this->sitemaps;
    }
    
    
    /**
     * Return the localized content allow robots statuses
     *
     * @return ContentAllowRobotsStatusCollection|null
     */
    public function allowRobotsStatuses(): ?ContentAllowRobotsStatusCollection
    {
        return $this->allowRobotsStatuses;
    }
    
    
    /**
     * @return ContentOpengraphImageCollection|null
     */
    public function opengraphImages(): ?ContentOpengraphImageCollection
    {
        return null;
    }
    
    
    /**
     * @return bool
     */
    public function isDeletable(): bool
    {
        return $this->deletable;
    }
    
    
    /**
     * @return ContentIdentificationInterface|null
     */
    public function id(): ?ContentIdentificationInterface
    {
        return $this->id;
    }
    
    
    /**
     * @return ContentNameCollection|null
     */
    public function names(): ?ContentNameCollection
    {
        return $this->names;
    }
    
    
    /**
     * @return int
     */
    public function order(): int
    {
        return $this->sortOrder;
    }
    
    
    /**
     * @return ContentOpenInNewTabStatusCollection|null
     */
    public function openInNewTab(): ?ContentOpenInNewTabStatusCollection
    {
        return $this->openInNewTab;
    }
}