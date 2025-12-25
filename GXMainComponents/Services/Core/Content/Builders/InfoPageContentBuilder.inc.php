<?php
/* --------------------------------------------------------------
   InfoPageContentBuilder.inc.php 2022-10-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class InfoPageContentBuilder
 *
 * This class represents an info page content builder
 *
 * @category   System
 * @package    Content
 */
class InfoPageContentBuilder implements BuilderInterface, InfoPageContentBuilderInterface
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
     * @var ContentTextCollection
     */
    protected $texts;
    
    /**
     * Localized content download files
     *
     * @var ContentDownloadFileCollection
     */
    protected $downloads;
    
    /**
     * Localized content meta titles
     *
     * @var ContentMetaTitleCollection
     */
    protected $metaTitles;
    
    /**
     * Localized content meta keywords
     *
     * @var ContentMetaKeywordsCollection
     */
    protected $metaKeywords;
    
    /**
     * Localized content OpenGraph images
     *
     * @var ContentOpengraphImageCollection
     */
    protected $opengraphImages;
    
    /**
     * Localized content meta descriptions
     *
     * @var ContentMetaDescriptionCollection
     */
    protected $metaDescriptions;
    
    /**
     * "Allow Robots" status
     *
     * @var ContentAllowRobotsStatus
     */
    protected $allowRobotsStatuses;
    
    /**
     * Content site map information
     *
     * @var ContentSitemap
     */
    protected $sitemaps;
    
    /**
     * Localized content URL rewrites
     *
     * @var ContentUrlRewriteCollection
     */
    protected $urlRewrites;
    
    /**
     * Localized content URL keywords
     *
     * @var ContentUrlKeywordsCollection
     */
    protected $urlKeywords;
    
    /**
     * Localized content names
     *
     * @var ContentNameCollection
     */
    protected $names;
    
    /**
     * Content group id
     *
     * @var ContentIdentificationInterface|null
     */
    protected $id;
    
    /**
     * Content sort order
     *
     * @var ContentSortOrderInterface
     */
    protected $sortOrder;
    
    /**
     * Content protected
     *
     * @var ContentDeleteInterface
     */
    protected $deletable;
    
    
    /**
     * Return new instance of the builder
     *
     * @return InfoPageContentBuilder New instance
     */
    public static function create(): self
    {
        return new self;
    }
    
    
    /**
     * Return the created InfoPageContent instance
     *
     * @return InfoPageContent Info page content entity object
     *
     * @throws UnfinishedBuildException On calling build() before having all properties set
     */
    public function build(): InfoPageContent
    {
        $properties = [
            'position'          => $this->position,
            'status'            => $this->status,
            'titles'            => $this->titles,
            'heading'           => $this->headings,
            'texts'             => $this->texts,
            'names'             => $this->names,
            'id'                => $this->id,
            'downloads'         => $this->downloads,
            'metaTitles'        => $this->metaTitles,
            'metaKeywords'      => $this->metaKeywords,
            'opengraphImages'   => $this->opengraphImages,
            'metaDescriptions'  => $this->metaDescriptions,
            'allowRobotsStatus' => $this->allowRobotsStatuses,
            'sitemaps'          => $this->sitemaps,
            'urlRewrites'       => $this->urlRewrites,
            'urlKeywords'       => $this->urlKeywords,
            'sortOrder'         => $this->sortOrder,
            'deletable'         => $this->deletable,
        ];
        
        $requiredProperties = ['position', 'status', 'titles', 'heading', 'texts', 'names'];
        
        foreach ($requiredProperties as $name) {
            if (null === $properties[$name]) {
                throw new UnfinishedBuildException("Property {$name} is not set");
            }
        }
        
        $collectionProperties = [
            'names',
            'titles',
            'heading',
            'texts',
            'downloads',
            'metaTitles',
            'metaKeywords',
            'metaDescriptions',
            'urlKeywords',
            'urlRewrites',
            'opengraphImages',
            'status'
        ];
        
        foreach ($collectionProperties as $name) {
            if (isset($properties[$name]) && $properties[$name]->isEmpty()) {
                throw new UnfinishedBuildException("field {$name} must have at least one language!");
            }
        }
        
        return new InfoPageContent(...array_values($properties));
    }
    
    
    /**
     * Set the page position
     *
     * @param PagePosition $position page position
     *
     * @return InfoPageContentBuilder Same instance
     */
    public function inPosition(PagePosition $position = null): self
    {
        $this->position = $position;
        
        return $this;
    }
    
    
    /**
     * Set the activation status
     *
     * @param ContentStatusCollection $status Activation status
     *
     * @return InfoPageContentBuilder Same instance
     */
    public function usingStatus(ContentStatusCollection $status = null): self
    {
        $this->status = $status;
        
        return $this;
    }
    
    
    /**
     * Set the localized content titles
     *
     * @param ContentTitleCollection $titles Localized titles
     *
     * @return InfoPageContentBuilder Same instance
     */
    public function usingTitles(ContentTitleCollection $titles = null): self
    {
        $this->titles = $titles;
        
        return $this;
    }
    
    
    /**
     * Set the localized content headings
     *
     * @param ContentHeadingCollection $headings Localized headings
     *
     * @return InfoPageContentBuilder Same instance
     */
    public function usingHeadings(ContentHeadingCollection $headings = null): self
    {
        $this->headings = $headings;
        
        return $this;
    }
    
    
    /**
     * Set the localized content texts
     *
     * @param ContentTextCollection $texts Localized texts
     *
     * @return InfoPageContentBuilder Same instance
     */
    public function usingTexts(ContentTextCollection $texts = null): self
    {
        $this->texts = $texts;
        
        return $this;
    }
    
    
    /**
     * Set the localized content download files
     *
     * @param ContentDownloadFileCollection $downloads Localized download files
     *
     * @return InfoPageContentBuilder Same instance
     */
    public function usingDownloadFiles(ContentDownloadFileCollection $downloads = null): self
    {
        $this->downloads = $downloads;
        
        return $this;
    }
    
    
    /**
     * Set the localized content meta titles
     *
     * @param ContentMetaTitleCollection $metaTitles Localized meta titles
     *
     * @return InfoPageContentBuilder Same instance
     */
    public function usingMetaTitles(ContentMetaTitleCollection $metaTitles = null): self
    {
        $this->metaTitles = $metaTitles;
        
        return $this;
    }
    
    
    /**
     * Set the localized content meta keywords
     *
     * @param ContentMetaKeywordsCollection $metaKeywords Localized meta keywords
     *
     * @return InfoPageContentBuilder Same instance
     */
    public function usingMetaKeywords(ContentMetaKeywordsCollection $metaKeywords = null): self
    {
        $this->metaKeywords = $metaKeywords;
        
        return $this;
    }
    
    
    /**
     * Set the localized content OpenGraph images
     *
     * @param ContentOpengraphImageCollection $opengraphImages Localized OpenGraph images
     *
     * @return InfoPageContentBuilder Same instance
     */
    public function usingOpengraphImages(ContentOpengraphImageCollection $opengraphImages = null): self
    {
        $this->opengraphImages = $opengraphImages;
        
        return $this;
    }
    
    
    /**
     * Set the localized content meta descriptions
     *
     * @param ContentMetaDescriptionCollection $metaDescriptions Localized meta descriptions
     *
     * @return InfoPageContentBuilder Same instance
     */
    public function usingMetaDescriptions(ContentMetaDescriptionCollection $metaDescriptions = null): self
    {
        $this->metaDescriptions = $metaDescriptions;
        
        return $this;
    }
    
    
    /**
     * Set the content "Allow robots" status
     *
     * @param ContentAllowRobotsStatusCollection|null $allowRobotsStatus "Allow robots" status
     *
     * @return InfoPageContentBuilder Same instance
     */
    public function usingAllowRobotsStatuses(ContentAllowRobotsStatusCollection $allowRobotsStatus = null): self
    {
        $this->allowRobotsStatuses = $allowRobotsStatus;
        
        return $this;
    }
    
    
    /**
     * Set the content sitemap information
     *
     * @param ContentSitemapCollection|null $sitemaps Sitemap information
     *
     * @return InfoPageContentBuilder Same instance
     */
    public function usingSitemaps(?ContentSitemapCollection $sitemaps = null): self
    {
        $this->sitemaps = $sitemaps;
        
        return $this;
    }
    
    
    /**
     * Set the localized content URL rewrite
     *
     * @param ContentUrlRewriteCollection $urlRewrites Localized URL rewrites
     *
     * @return InfoPageContentBuilder Same instance
     */
    public function usingUrlRewrites(ContentUrlRewriteCollection $urlRewrites = null): self
    {
        $this->urlRewrites = $urlRewrites;
        
        return $this;
    }
    
    
    /**
     * Set the localized content URL keywords
     *
     * @param ContentUrlKeywordsCollection $urlKeywords Localized URL keywords
     *
     * @return InfoPageContentBuilder Same instance
     */
    public function usingUrlKeywords(ContentUrlKeywordsCollection $urlKeywords = null): self
    {
        $this->urlKeywords = $urlKeywords;
        
        return $this;
    }
    
    
    /**
     * Set the localized content names
     *
     * @param ContentNameCollection $names Localized names
     *
     * @return InfoPageContentBuilder Same instance
     */
    public function usingNames(ContentNameCollection $names = null): self
    {
        $this->names = $names;
        
        return $this;
    }
    
    
    /**
     * Set the content group id
     *
     * @param ContentIdentificationInterface|null $id
     *
     * @return InfoPageContentBuilder Same instance
     */
    public function usingId(ContentIdentificationInterface $id = null): InfoPageContentBuilder
    {
        $this->id = $id;
        
        return $this;
    }
    
    
    /**
     * @inheritDoc
     */
    public function usingSortOrder(ContentSortOrderInterface $sortOrder = null): InfoPageContentBuilder
    {
        $this->sortOrder = $sortOrder;
    
        return $this;
    }
    
    
    /**
     * @inheritDoc
     */
    public function usingDelete(ContentDeleteInterface $deletable = null): InfoPageContentBuilder
    {
        $this->deletable = $deletable;
    
        return $this;
    }
}
