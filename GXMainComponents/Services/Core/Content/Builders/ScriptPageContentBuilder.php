<?php
/*--------------------------------------------------------------------------------------------------
    ScriptPageContentBuilder.php 2023-03-06
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2023 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

declare(strict_types=1);


/**
 * Class ScriptPageContentBuilder
 */
class ScriptPageContentBuilder implements BuilderInterface, ScriptPageContentBuilderInterface
{
    /**
     * Localized content names
     *
     * @var ContentNameCollection
     */
    protected $names;
    
    /**
     * Localized content titles
     *
     * @var ContentTitleCollection
     */
    protected $titles;
    
    /**
     * Content position
     *
     * @var PagePosition
     */
    protected $position;
    
    /**
     * Content group id
     *
     * @var ContentIdentificationInterface|null
     */
    protected $id;
    
    /**
     * Content status
     *
     * @var ContentStatusCollection
     */
    protected $status;
    
    /**
     * "Allow Robots" status
     *
     * @var ContentAllowRobotsStatusCollection
     */
    protected $allowRobots;
    
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
    protected $metaKeyword;
    
    /**
     * Localized content meta titles
     *
     * @var ContentMetaTitleCollection
     */
    protected $metaTitle;
    
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
    protected $urlRewrite;
    
    /**
     * Content site map information
     *
     * @var ContentSitemapCollection
     */
    protected $sitemaps;
    
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
     * Whether the link page is opened in a new tab
     *
     * @var ContentOpenInNewTabStatus
     */
    protected $openInNewTab;
    
    /**
     * Localized content script file
     *
     * @var ContentScriptFileCollection
     */
    protected $scriptFiles;
    
    
    /**
     * @inheritDoc
     */
    public static function create(): ScriptPageContentBuilder
    {
        return new self;
    }
    
    
    /**
     * @inheritDoc
     */
    public function build(): ScriptPageContent
    {
        $properties = [
            'names'            => $this->names,
            'titles'           => $this->titles,
            'position'         => $this->position,
            'status'           => $this->status,
            'scriptFiles'      => $this->scriptFiles,
            'id'               => $this->id,
            'allowRobots'      => $this->allowRobots,
            'metaDescriptions' => $this->metaDescriptions,
            'metaKeywords'     => $this->metaKeyword,
            'metaTitles'       => $this->metaTitle,
            'urlKeywords'      => $this->urlKeywords,
            'urlRewrites'      => $this->urlRewrite,
            'sitemaps'         => $this->sitemaps,
            'sortOrder'        => $this->sortOrder,
            'deletable'        => $this->deletable,
            'openInNewTab'     => $this->openInNewTab,
        ];
        
        $requiredProperties = ['position', 'status', 'titles', 'scriptFiles', 'names'];
        
        foreach ($requiredProperties as $name) {
            if (null === $properties[$name]) {
                throw new UnfinishedBuildException("Property {$name} is not set");
            }
        }
        
        $collectionProperties = [
            'names',
            'titles',
            'heading',
            'scriptFiles',
            'metaTitles',
            'metaKeywords',
            'metaDescriptions',
            'urlKeywords',
            'urlRewrites',
            'status',
        ];
        
        foreach ($collectionProperties as $name) {
            if (isset($properties[$name]) && $properties[$name]->isEmpty()) {
                throw new UnfinishedBuildException("field {$name} must have at least one language!");
            }
        }
        
        return new ScriptPageContent(...array_values($properties));
    }
    
    
    /**
     * @param PagePosition $position
     *
     * @return $this
     */
    public function inPosition(PagePosition $position): ScriptPageContentBuilder
    {
        $this->position = $position;
        
        return $this;
    }
    
    
    /**
     * @param ContentStatusCollection $status
     *
     * @return $this
     */
    public function usingStatus(ContentStatusCollection $status): ScriptPageContentBuilder
    {
        $this->status = $status;
        
        return $this;
    }
    
    
    /**
     * @param ContentTitleCollection $titles
     *
     * @return $this
     */
    public function usingTitles(ContentTitleCollection $titles): ScriptPageContentBuilder
    {
        $this->titles = $titles;
        
        return $this;
    }
    
    
    /**
     * @param ContentTextCollection $texts
     *
     * @return $this
     */
    public function usingTexts(ContentTextCollection $texts): ScriptPageContentBuilder
    {
        $this->texts = $texts;
        
        return $this;
    }
    
    
    /**
     * @param ContentScriptFileCollection $scripts
     *
     * @return $this
     */
    public function usingScriptFiles(ContentScriptFileCollection $scripts): ScriptPageContentBuilder
    {
        $this->scriptFiles = $scripts;
        
        return $this;
    }
    
    
    /**
     * @param ContentMetaTitleCollection $metaTitles
     *
     * @return $this
     */
    public function usingMetaTitles(ContentMetaTitleCollection $metaTitles): ScriptPageContentBuilder
    {
        $this->metaTitle = $metaTitles;
        
        return $this;
    }
    
    
    /**
     * @param ContentMetaKeywordsCollection $metaKeywords
     *
     * @return $this
     */
    public function usingMetaKeywords(ContentMetaKeywordsCollection $metaKeywords): ScriptPageContentBuilder
    {
        $this->metaKeyword = $metaKeywords;
        
        return $this;
    }
    
    
    /**
     * @param ContentMetaDescriptionCollection $metaDescriptions
     *
     * @return $this
     */
    public function usingMetaDescriptions(ContentMetaDescriptionCollection $metaDescriptions): ScriptPageContentBuilder
    {
        $this->metaDescriptions = $metaDescriptions;
        
        return $this;
    }
    
    
    /**
     * @param ContentAllowRobotsStatusCollection $allowRobotsStatuses
     *
     * @return $this
     */
    public function usingAllowRobotsStatus(ContentAllowRobotsStatusCollection $allowRobotsStatuses): ScriptPageContentBuilder
    {
        $this->allowRobots = $allowRobotsStatuses;
        
        return $this;
    }
    
    
    /**
     * @param ContentSitemapCollection $sitemaps
     *
     * @return $this
     */
    public function usingSitemaps(ContentSitemapCollection $sitemaps): ScriptPageContentBuilder
    {
        $this->sitemaps = $sitemaps;
        
        return $this;
    }
    
    
    /**
     * @param ContentUrlRewriteCollection $urlRewrites
     *
     * @return $this
     */
    public function usingUrlRewrites(ContentUrlRewriteCollection $urlRewrites): ScriptPageContentBuilder
    {
        $this->urlRewrite = $urlRewrites;
        
        return $this;
    }
    
    
    /**
     * @param ContentUrlKeywordsCollection $urlKeywords
     *
     * @return $this
     */
    public function usingUrlKeywords(ContentUrlKeywordsCollection $urlKeywords): ScriptPageContentBuilder
    {
        $this->urlKeywords = $urlKeywords;
        
        return $this;
    }
    
    
    /**
     * @param ContentNameCollection $names
     *
     * @return $this
     */
    public function usingNames(ContentNameCollection $names): ScriptPageContentBuilder
    {
        $this->names = $names;
        
        return $this;
    }
    
    
    /**
     * @param ContentIdentificationInterface|null $id
     *
     * @return $this
     */
    public function usingId(ContentIdentificationInterface $id = null): ScriptPageContentBuilder
    {
        $this->id = $id;
        
        return $this;
    }
    
    
    /**
     * @param ContentSortOrderInterface|null $sortOrder
     *
     * @return $this
     */
    public function usingSortOrder(ContentSortOrderInterface $sortOrder = null): ScriptPageContentBuilder
    {
        $this->sortOrder = $sortOrder;
        
        return $this;
    }
    
    
    /**
     * @param ContentDeleteInterface|null $deletable
     *
     * @return $this
     */
    public function usingDelete(ContentDeleteInterface $deletable = null): ScriptPageContentBuilder
    {
        $this->deletable = $deletable;
        
        return $this;
    }
    
    
    /**
     * @param ContentOpenInNewTabStatusCollection $openInNewTabStatus
     *
     * @return $this
     */
    public function usingOpenInNewTabStatus(ContentOpenInNewTabStatusCollection $openInNewTabStatus): ScriptPageContentBuilder
    {
        $this->openInNewTab = $openInNewTabStatus;
        
        return $this;
    }
}