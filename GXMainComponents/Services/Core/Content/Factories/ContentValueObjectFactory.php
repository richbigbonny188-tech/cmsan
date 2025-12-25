<?php
/* --------------------------------------------------------------
  ContentValueObjectFactory.php 2021-08-18
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2021 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

/**
 * Class ContentValueObjectFactory
 */
class ContentValueObjectFactory
{
    /**
     * @var LanguageProviderInterface
     */
    protected $languageProvider;
    
    
    /**
     * ContentValueObjectFactory constructor.
     *
     * @param LanguageProviderInterface $languageProvider
     */
    public function __construct(LanguageProviderInterface $languageProvider)
    {
        $this->languageProvider = $languageProvider;
    }
    
    
    /**
     * @return LanguageProviderInterface
     */
    public function languageProvider(): LanguageProviderInterface
    {
        return $this->languageProvider;
    }
    
    
    /**
     * @param int $languageId
     *
     * @return LanguageCode
     */
    public function getLanguageCodeFromLanguageId(int $languageId): LanguageCode
    {
        return $this->languageProvider()->getCodeById(new IdType($languageId));
    }
    
    
    /**
     * @param array $dbContent
     *
     * @return array
     * @throws Exception
     */
    public function createValueObjectsForInfoElementContent(array $dbContent): array
    {
        $position = ElementPosition::createFromString(ElementPositionMapper::getElementPositionFromDatabase($dbContent[0]['content_position']));
        $headings = $this->createContentHeadingCollection($dbContent);
        $id       = (int)$dbContent[0]['content_group'];
        $alias    = $dbContent[0]['content_alias'];
        $status   = $this->createContentStatusCollection($dbContent);
        $texts    = $this->createContentTextCollection($dbContent);
        $titles   = $this->createContentTitleCollection($dbContent);
        
        return [
            'position' => $position,
            'headings' => $headings,
            'id'       => $id,
            'alias'    => $alias,
            'status'   => $status,
            'texts'    => $texts,
            'titles'   => $titles,
        ];
    }
    
    
    /**
     * @param array $dbContent
     * @param array $urlRewriteData
     *
     * @return array
     * @throws Exception
     */
    public function createValueObjectsForInfoPageContent(array $dbContent, array $urlRewriteData): array
    {
        $heading          = $this->createContentHeadingCollection($dbContent);
        $texts            = $this->createContentTextCollection($dbContent);
        $position         = PagePosition::createFromString(PagePositionMapper::getPagePositionFromDatabase($dbContent[0]['content_position']));
        $id               = (int)$dbContent[0]['content_group'];
        $alias            = $dbContent[0]['content_alias'];
        $status           = $this->createContentStatusCollection($dbContent);
        $titles           = $this->createContentTitleCollection($dbContent);
        $allowRobots      = $this->createContentAllowRobotsStatusCollection($dbContent);
        $downloadFiles    = $this->createDownloadFileCollection($dbContent);
        $metaDescriptions = $this->createContentMetaDescriptionCollection($dbContent);
        $metaKeyword      = $this->createContentMetaKeywordsCollection($dbContent);
        $metaTitle        = $this->createContentMetaTitleCollection($dbContent);
        $names            = $this->createContentNameCollection($dbContent);
        $openGraph        = $this->createContentOpenGraphImageCollection($dbContent);
        $urlKeywords      = $this->createContentUrlKeywordsCollection($dbContent);
        $urlRewrite       = $this->createContentUrlRewriteCollection($urlRewriteData);
        $sitemaps         = $this->createContentSitemapCollection($dbContent);
        $sortOrder        = $this->createContentSortOrder($dbContent);
        $deletable        = $this->createContentDelete($dbContent);
        
        return [
            'headings'         => $heading,
            'texts'            => $texts,
            'position'         => $position,
            'id'               => $id,
            'alias'            => $alias,
            'status'           => $status,
            'titles'           => $titles,
            'allowRobots'      => $allowRobots,
            'downloadFiles'    => $downloadFiles,
            'metaDescriptions' => $metaDescriptions,
            'metaKeywords'     => $metaKeyword,
            'metaTitles'       => $metaTitle,
            'names'            => $names,
            'openGraph'        => $openGraph,
            'urlKeywords'      => $urlKeywords,
            'urlRewrites'      => $urlRewrite,
            'sitemaps'         => $sitemaps,
            'sortOrder'        => $sortOrder,
            'deletable'        => $deletable,
        ];
    }
    
    
    /**
     * @param array $dbContent
     *
     * @return array
     * @throws Exception
     */
    public function createValueObjectsForLinkPageContent(array $dbContent): array
    {
        $names        = $this->createContentNameCollection($dbContent);
        $position     = PagePosition::createFromString(PagePositionMapper::getPagePositionFromDatabase($dbContent[0]['content_position']));
        $titles       = $this->createContentTitleCollection($dbContent);
        $links        = $this->createContentLinkCollection($dbContent);
        $openInNewTab = $this->createContentOpenInNewTabStatusCollection($dbContent);
        $status       = $this->createContentStatusCollection($dbContent);
        $id           = (int)$dbContent[0]['content_group'];
        $alias        = (int)$dbContent[0]['content_alias'];
        $sortOrder    = $this->createContentSortOrder($dbContent);
        $deletable    = $this->createContentDelete($dbContent);
        
        return [
            'names'        => $names,
            'position'     => $position,
            'titles'       => $titles,
            'links'        => $links,
            'openInNewTab' => $openInNewTab,
            'status'       => $status,
            'id'           => $id,
            'alias'        => $alias,
            'sortOrder'    => $sortOrder,
            'deletable'    => $deletable,
        ];
    }
    
    
    /**
     * @param array $dbContent
     * @param array $urlRewriteData
     *
     * @return array
     * @throws Exception
     */
    public function createValueObjectsForFilePageContent(array $dbContent, array $urlRewriteData): array
    {
        $names            = $this->createContentNameCollection($dbContent);
        $titles           = $this->createContentTitleCollection($dbContent);
        $status           = $this->createContentStatusCollection($dbContent);
        $openInNewTab     = $this->createContentOpenInNewTabStatusCollection($dbContent);
        $metaDescriptions = $this->createContentMetaDescriptionCollection($dbContent);
        $metaKeyword      = $this->createContentMetaKeywordsCollection($dbContent);
        $metaTitle        = $this->createContentMetaTitleCollection($dbContent);
        $urlKeywords      = $this->createContentUrlKeywordsCollection($dbContent);
        $urlRewrite       = $this->createContentUrlRewriteCollection($urlRewriteData);
        $sitemaps         = $this->createContentSitemapCollection($dbContent);
        $sortOrder        = $this->createContentSortOrder($dbContent);
        $deletable        = $this->createContentDelete($dbContent);
        $allowRobots      = $this->createContentAllowRobotsStatusCollection($dbContent);
        $scriptFiles      = $this->createScriptFileCollection($dbContent);
        $position         = PagePosition::createFromString(PagePositionMapper::getPagePositionFromDatabase($dbContent[0]['content_position']));
        $id               = (int)$dbContent[0]['content_group'];
        $alias            = $dbContent[0]['content_alias'];
        
        return [
            'names'            => $names,
            'titles'           => $titles,
            'position'         => $position,
            'id'               => $id,
            'alias'            => $alias,
            'status'           => $status,
            'allowRobots'      => $allowRobots,
            'metaDescriptions' => $metaDescriptions,
            'metaKeywords'     => $metaKeyword,
            'metaTitles'       => $metaTitle,
            'urlKeywords'      => $urlKeywords,
            'urlRewrites'      => $urlRewrite,
            'sitemaps'         => $sitemaps,
            'sortOrder'        => $sortOrder,
            'deletable'        => $deletable,
            'openInNewTab'     => $openInNewTab,
            'scriptFiles'      => $scriptFiles,
        ];
    }
    
    
    /**
     * @param array $dbContent
     *
     * @return ContentSitemapCollection
     */
    public function createContentSitemapCollection(array $dbContent): ContentSitemapCollection
    {
        $sitemaps = [];
        foreach ($dbContent as $content) {
            
            $languageCode = $this->getLanguageCodeFromLanguageId((int)$content['languages_id']);
            
            $sitemaps[] = new ContentSitemap(new ContentSitemapVisibility((bool)$content['gm_sitemap_entry']),
                                             new ContentSitemapPriority((float)$content['gm_priority']),
                                             ContentSitemapChangeFrequency::createFromString($content['gm_changefreq'] ? : ContentSitemapChangeFrequency::ALWAYS),
                                             $languageCode);
        }
        
        return new ContentSitemapCollection($sitemaps);
    }
    
    
    /**
     * @param array $dbContent
     *
     * @return ContentOpenInNewTabStatusCollection
     */
    public function createContentOpenInNewTabStatusCollection(array $dbContent): ContentOpenInNewTabStatusCollection
    {
        $statuses = [];
        foreach ($dbContent as $content) {
            $languageCode = $this->getLanguageCodeFromLanguageId((int)$content['languages_id']);
            $statuses[] = new ContentOpenInNewTabStatus($content['gm_link_target'] === '_blank',
                                                       $languageCode);
        }
    
        return new ContentOpenInNewTabStatusCollection($statuses);
    }
    
    
    /**
     * @param array $dbContent
     *
     * @return ContentLinkCollection
     */
    public function createContentLinkCollection(array $dbContent): ContentLinkCollection
    {
        $urlRewriteStd = new stdClass;
        
        foreach ($dbContent as $content) {
            
            $languageCode = $this->getLanguageCodeFromLanguageId((int)$content['languages_id']);
            
            $urlRewriteStd->{$languageCode->asString()} = $content['gm_link'];
        }
        
        return ThemeContentsParser::createLocalizedCollection($urlRewriteStd, new StringType(ContentLink::class));
    }
    
    
    /**
     * @param array $dbContent
     *
     * @return ContentUrlRewriteCollection
     */
    public function createContentUrlRewriteCollection(array $dbContent): ContentUrlRewriteCollection
    {
        $urlRewriteStd = new stdClass;
        
        foreach ($dbContent as $content) {
            
            $languageCode = $this->getLanguageCodeFromLanguageId((int)$content['language_id']);
            
            $urlRewriteStd->{$languageCode->asString()} = $content['rewrite_url'];
        }
        
        return ThemeContentsParser::createLocalizedCollection($urlRewriteStd, new StringType(ContentUrlRewrite::class));
    }
    
    
    /**
     * @param array $dbContent
     *
     * @return ContentUrlKeywordsCollection
     */
    public function createContentUrlKeywordsCollection(array $dbContent): ContentUrlKeywordsCollection
    {
        $urlKeywordsStd = new stdClass;
        
        foreach ($dbContent as $content) {
            
            $languageCode = $this->getLanguageCodeFromLanguageId((int)$content['languages_id']);
            
            $urlKeywordsStd->{$languageCode->asString()} = $content['gm_url_keywords'];
        }
        
        return ThemeContentsParser::createLocalizedCollection($urlKeywordsStd,
                                                              new StringType(ContentUrlKeywords::class));
    }
    
    
    /**
     * @param array $dbContent
     *
     * @return ContentOpenGraphImageCollection
     */
    public function createContentOpenGraphImageCollection(array $dbContent): ContentOpenGraphImageCollection
    {
        $openGraphImageStd = new stdClass;
        
        foreach ($dbContent as $content) {
            
            $languageCode = $this->getLanguageCodeFromLanguageId((int)$content['languages_id']);
            
            $openGraphImageStd->{$languageCode->asString()} = $content['opengraph_image'];
        }
        
        return ThemeContentsParser::createLocalizedCollection($openGraphImageStd,
                                                              new StringType(ContentOpengraphImage::class));
    }
    
    
    /**
     * @param array $dbContent
     *
     * @return ContentNameCollection
     */
    public function createContentNameCollection(array $dbContent): ContentNameCollection
    {
        $contentNameStd = new stdClass;
        
        foreach ($dbContent as $content) {
            
            $languageCode = $this->getLanguageCodeFromLanguageId((int)$content['languages_id']);
            
            $contentNameStd->{$languageCode->asString()} = $content['content_name'];
        }
        
        return ThemeContentsParser::createLocalizedCollection($contentNameStd, new StringType(ContentName::class));
    }
    
    
    /**
     * @param array $dbContent
     *
     * @return ContentMetaTitleCollection
     */
    public function createContentMetaTitleCollection(array $dbContent): ContentMetaTitleCollection
    {
        $metaTitleStd = new stdClass;
        
        foreach ($dbContent as $content) {
            
            $languageCode = $this->getLanguageCodeFromLanguageId((int)$content['languages_id']);
            
            $metaTitleStd->{$languageCode->asString()} = $content['contents_meta_title'];
        }
        
        return ThemeContentsParser::createLocalizedCollection($metaTitleStd, new StringType(ContentMetaTitle::class));
    }
    
    
    /**
     * @param array $dbContent
     *
     * @return ContentMetaKeywordsCollection
     */
    public function createContentMetaKeywordsCollection(array $dbContent): ContentMetaKeywordsCollection
    {
        $metaKeywordsStd = new stdClass;
        
        foreach ($dbContent as $content) {
            
            $languageCode = $this->getLanguageCodeFromLanguageId((int)$content['languages_id']);
            
            $metaKeywordsStd->{$languageCode->asString()} = $content['contents_meta_keywords'];
        }
        
        return ThemeContentsParser::createLocalizedCollection($metaKeywordsStd,
                                                              new StringType(ContentMetaKeywords::class));
    }
    
    
    /**
     * @param array $dbContent
     *
     * @return ContentMetaDescriptionCollection
     */
    public function createContentMetaDescriptionCollection(array $dbContent): ContentMetaDescriptionCollection
    {
        $metaDescriptionStd = new stdClass;
        
        foreach ($dbContent as $content) {
            
            $languageCode = $this->getLanguageCodeFromLanguageId((int)$content['languages_id']);
            
            $metaDescriptionStd->{$languageCode->asString()} = $content['contents_meta_description'];
        }
        
        return ThemeContentsParser::createLocalizedCollection($metaDescriptionStd,
                                                              new StringType(ContentMetaDescription::class));
    }
    
    
    /**
     * @param array $dbContent
     *
     * @return ContentScriptFileCollection
     */
    public function createScriptFileCollection(array $dbContent): ContentScriptFileCollection
    {
        $scriptFileStd = new stdClass;
        
        foreach ($dbContent as $content) {
            
            $languageCode = $this->getLanguageCodeFromLanguageId((int)$content['languages_id']);
            
            $scriptFileStd->{$languageCode->asString()} = $content['content_file'];
        }
        
        return ThemeContentsParser::createLocalizedCollection($scriptFileStd,
                                                              new StringType(ContentScriptFile::class));
    }
    
    
    /**
     * @param array $dbContent
     *
     * @return ContentDownloadFileCollection
     */
    public function createDownloadFileCollection(array $dbContent): ContentDownloadFileCollection
    {
        $downloadFileStd = new stdClass;
        
        foreach ($dbContent as $content) {
            
            $languageCode = $this->getLanguageCodeFromLanguageId((int)$content['languages_id']);
            
            $downloadFileStd->{$languageCode->asString()} = $content['download_file'];
        }
        
        return ThemeContentsParser::createLocalizedCollection($downloadFileStd,
                                                              new StringType(ContentDownloadFile::class));
    }
    
    
    /**
     * @param array $dbContent
     *
     * @return ContentAllowRobotsStatusCollection
     */
    public function createContentAllowRobotsStatusCollection(array $dbContent): ContentAllowRobotsStatusCollection
    {
        $statuses = [];
        foreach ($dbContent as $content) {
            $languageCode = $this->getLanguageCodeFromLanguageId((int)$content['languages_id']);
            $statuses[] = new ContentAllowRobotsStatus((bool)$content['gm_robots_entry'],
                                             $languageCode);
        }
    
        return new ContentAllowRobotsStatusCollection($statuses);
    }
    
    
    /**
     * @param array $dbContent
     *
     * @return ContentHeadingCollection
     */
    public function createContentHeadingCollection(array $dbContent): ContentHeadingCollection
    {
        $headingStd = new stdClass;
        
        foreach ($dbContent as $content) {
            
            $languageCode = $this->getLanguageCodeFromLanguageId((int)$content['languages_id']);
            
            $headingStd->{$languageCode->asString()} = $content['content_heading'];
        }
        
        return ThemeContentsParser::createLocalizedCollection($headingStd, new StringType(ContentHeading::class));
    }
    
    
    /**
     * @param array $dbContent
     *
     * @return ContentStatus
     */
    public function createContentStatus(array $dbContent): ContentStatus
    {
        $firstContent = current($dbContent);
        
        return new ContentStatus((bool)$firstContent['content_status']);
    }
    
    
    /**
     * @param array $dbContent
     *
     * @return ContentStatusCollection
     */
    public function createContentStatusCollection(array $dbContent): ContentStatusCollection
    {
        $statusStd = new stdClass;
        
        foreach ($dbContent as $content) {
            
            $languageCode = $this->getLanguageCodeFromLanguageId((int)$content['languages_id']);
            
            $statusStd->{$languageCode->asString()} = $content['content_status'];
        }
        
        return ThemeContentsParser::createLocalizedCollection($statusStd, new StringType(ContentStatus::class));
    }
    
    
    /**
     * @param array $dbContent
     *
     * @return ContentTextCollection
     */
    public function createContentTextCollection(array $dbContent): ContentTextCollection
    {
        $textStd = new stdClass;
        
        foreach ($dbContent as $content) {
            
            $languageCode = $this->getLanguageCodeFromLanguageId((int)$content['languages_id']);
            
            $textStd->{$languageCode->asString()} = $content['content_text'];
        }
        
        return ThemeContentsParser::createLocalizedCollection($textStd, new StringType(ContentText::class));
    }
    
    
    /**
     * @param array $dbContent
     *
     * @return ContentTitleCollection
     */
    public function createContentTitleCollection(array $dbContent): ContentTitleCollection
    {
        $titleStd = new stdClass;
        
        foreach ($dbContent as $content) {
            
            $languageCode = $this->getLanguageCodeFromLanguageId((int)$content['languages_id']);
            
            $titleStd->{$languageCode->asString()} = $content['content_title'];
        }
        
        return ThemeContentsParser::createLocalizedCollection($titleStd, new StringType(ContentTitle::class));
    }
    
    
    /**
     * @param array $dbContent
     *
     * @return ContentSortOrderInterface
     */
    public function createContentSortOrder(array $dbContent): ContentSortOrderInterface
    {
        $firstContent = current($dbContent);
        
        return new ContentSortOrder((int)$firstContent['sort_order']);
    }
    
    
    /**
     * @param array $dbContent
     *
     * @return ContentDeleteInterface
     */
    public function createContentDelete(array $dbContent): ContentDeleteInterface
    {
        $firstContent = current($dbContent);
        
        return new ContentDelete($firstContent['content_delete'] === '1');
    }
}
