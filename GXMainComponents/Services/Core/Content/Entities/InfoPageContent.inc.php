<?php
/* --------------------------------------------------------------
   InfoPageContent.inc.php 2021-08-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class InfoPageContent
 *
 * This class represents an info page content
 *
 * @category   System
 * @package    Content
 */
class InfoPageContent
    implements ContentInterface, ContentAttributesInterface, ContentHeadingAttributeInterface, ContentTextAttributeInterface, ContentDownloadFileAttributeInterface, ContentMetaAttributesInterface, ContentNameAttributeInterface, ContentGroupIdInterface, ContentSortOrderInterface, ContentDeleteInterface
{
    /**
     * "Allow Robots" status
     *
     * @var bool
     */
    protected $allowRobots;
    /**
     * Localized content download files
     *
     * @var ContentHeadingCollection
     */
    protected $downloads;
    /**
     * Localized content headings
     *
     * @var ContentHeadingCollection
     */
    protected $headings;
    /**
     * Content group ID
     *
     * @var ContentIdentificationInterface
     */
    protected $id;
    /**
     * Localized content meta descriptions
     *
     * @var ContentMetaDescriptionCollection
     */
    protected $metaDescriptions;
    /**
     * Localized content meta keywords
     *
     * @var ContentMetaKeywordsCollection
     */
    protected $metaKeywords;
    /**
     * Localized content meta titles
     *
     * @var ContentMetaTitleCollection
     */
    protected $metaTitles;
    /**
     * Localized content names
     *
     * @var ContentNameCollection
     */
    protected $names;
    /**
     * Localized content OpenGraph images
     *
     * @var ContentOpengraphImageCollection
     */
    protected $opengraphImages;
    /**
     * Content position
     *
     * @var string
     */
    protected $position;
    /**
     * Localized content sitemap information
     *
     * @var ContentSitemapCollection
     */
    protected $sitemaps;
    /**
     * Content status
     *
     * @var ContentStatusCollection
     */
    protected $status;
    /**
     * Localized content texts
     *
     * @var ContentHeadingCollection
     */
    protected $texts;
    /**
     * Localized content titles
     *
     * @var ContentTitleCollection
     */
    protected $titles;
    /**
     * Content type
     *
     * @var string
     */
    protected $type;
    /**
     * Localized content URL keywords
     *
     * @var ContentUrlKeywordsCollection
     */
    protected $urlKeywords;
    /**
     * Localized content URL rewrites
     *
     * @var ContentUrlRewriteCollection
     */
    protected $urlRewrites;
    
    /**
     * @var int
     */
    protected $sortOrder;
    
    /**
     * @var bool
     */
    protected $deletable;
    
    
    /**
     * InfoPageContent constructor.
     *
     * @param ContentPositionInterface                $pagePosition
     * @param ContentStatusCollection                 $status
     * @param ContentTitleCollection                  $titles
     * @param ContentHeadingCollection                $headings
     * @param ContentTextCollection                   $texts
     * @param ContentNameCollection                   $names
     * @param ContentIdentificationInterface|null     $id Optional content group ID
     * @param ContentDownloadFileCollection|null      $downloads
     * @param ContentMetaTitleCollection|null         $metaTitles
     * @param ContentMetaKeywordsCollection|null      $metaKeywords
     * @param ContentOpengraphImageCollection|null    $opengraphImages
     * @param ContentMetaDescriptionCollection|null   $metaDescriptions
     * @param ContentAllowRobotsStatusCollection|null $allowRobotsStatuses
     * @param ContentSitemapCollection|null           $sitemaps
     * @param ContentUrlRewriteCollection|null        $urlRewrites
     * @param ContentUrlKeywordsCollection|null       $urlKeywords
     * @param ContentSortOrderInterface|null          $sortOrder
     * @param ContentDeleteInterface|null             $deletable
     */
    public function __construct(
        ContentPositionInterface $pagePosition,
        ContentStatusCollection $status,
        ContentTitleCollection             $titles,
        ContentHeadingCollection           $headings,
        ContentTextCollection              $texts,
        ContentNameCollection              $names,
        ContentIdentificationInterface     $id = null,
        ContentDownloadFileCollection      $downloads = null,
        ContentMetaTitleCollection         $metaTitles = null,
        ContentMetaKeywordsCollection      $metaKeywords = null,
        ContentOpengraphImageCollection    $opengraphImages = null,
        ContentMetaDescriptionCollection   $metaDescriptions = null,
        ContentAllowRobotsStatusCollection $allowRobotsStatuses = null,
        ContentSitemapCollection           $sitemaps = null,
        ContentUrlRewriteCollection        $urlRewrites = null,
        ContentUrlKeywordsCollection       $urlKeywords = null,
        ContentSortOrderInterface          $sortOrder = null,
        ContentDeleteInterface             $deletable = null
    ) {
        $this->type = ContentType::createForContent()->type();
        //required
        $this->position = $pagePosition->position();
        $this->status   = $status;
        $this->titles   = $titles;
        $this->headings = $headings;
        $this->texts    = $texts;
        $this->names    = $names;
        //not required
        $this->id               = $id ?? null;
        $this->downloads        = $downloads;
        $this->metaTitles       = $metaTitles;
        $this->metaKeywords     = $metaKeywords;
        $this->opengraphImages  = $opengraphImages;
        $this->metaDescriptions = $metaDescriptions;
        $this->allowRobots      = $allowRobotsStatuses;
        $this->sitemaps         = $sitemaps;
        $this->urlRewrites      = $urlRewrites;
        $this->urlKeywords      = $urlKeywords;
        $this->sortOrder        = $sortOrder ? $sortOrder->order() : 0;
        $this->deletable        = $deletable && $deletable->isDeletable();
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
     * Return the localized content download files
     *
     * @return ContentDownloadFileCollection
     */
    public function downloads(): ?ContentDownloadFileCollection
    {
        return $this->downloads;
    }
    
    
    /**
     * Return the localized content meta titles
     *
     * @return ContentMetaTitleCollection
     */
    public function metaTitles(): ?ContentMetaTitleCollection
    {
        return $this->metaTitles;
    }
    
    
    /**
     * Return the localized content meta titles
     *
     * @return ContentMetaKeywordsCollection
     */
    public function metaKeywords(): ?ContentMetaKeywordsCollection
    {
        return $this->metaKeywords;
    }
    
    
    /**
     * Return the localized content meta descriptions
     *
     * @return ContentMetaDescriptionCollection
     */
    public function metaDescriptions(): ?ContentMetaDescriptionCollection
    {
        return $this->metaDescriptions;
    }
    
    
    /**
     * Return the localized content URL keywords
     *
     * @return ContentUrlKeywordsCollection
     */
    public function urlKeywords(): ?ContentUrlKeywordsCollection
    {
        return $this->urlKeywords;
    }
    
    
    /**
     * Return the localized content URL rewrites
     *
     * @return ContentUrlRewriteCollection
     */
    public function urlRewrites(): ?ContentUrlRewriteCollection
    {
        return $this->urlRewrites;
    }
    
    
    /**
     * Return the localized content sitemap information
     *
     * @return ContentSitemapCollection
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
        return $this->allowRobots;
    }
    
    
    /**
     * Return the localized OpenGraph images
     *
     * @return ContentOpengraphImageCollection
     */
    public function opengraphImages(): ?ContentOpengraphImageCollection
    {
        return $this->opengraphImages;
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
     * Returns the content group id if set, otherwise null will be returned.
     *
     * @return ContentIdentificationInterface|null
     */
    public function id() : ?ContentIdentificationInterface
    {
        return $this->id;
    }
    
    
    /**
     * Returns the content sort order
     *
     * @return int
     */
    public function order() : int
    {
        return $this->sortOrder;
    }
    
    
    /**
     * Returns whether the content is protected
     *
     * @return bool
     */
    public function isDeletable() : bool
    {
        return $this->deletable;
    }
}
