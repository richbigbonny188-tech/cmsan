<?php
/*--------------------------------------------------------------------------------------------------
    ContentManagerPageTypeSaveCommand.php 2021-05-19
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2021 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\StyleEdit\Core\Components\ContentManager\Command;

use ContentIdentificationInterface;
use Gambio\StyleEdit\Core\Components\Theme\Entities\Interfaces\CurrentThemeInterface;
use Gambio\StyleEdit\Core\Helpers\ContentManagerHelper;
use Gambio\StyleEdit\Core\Language\Entities\Language;
use Gambio\StyleEdit\Core\Language\Services\LanguageService;
use GXModules\Gambio\StyleEdit\Core\Components\ContentManager\Services\ContentManagerPageTypeService;
use InfoPageContentBuilder;
use PagePosition;

/**
 * Class ContentManagerPageTypeSaveCommand
 *
 * @package Gambio\StyleEdit\Core\Components\ContentManager\Command
 */
class ContentManagerPageTypeSaveCommand extends AbstractContentManagerPagesSaveCommand
{
    /**
     * @var ContentManagerHelper
     */
    private $contentManagerHelper;
    
    /**
     * @var ContentManagerPageTypeService
     */
    private $pageTypeService;
    
    
    public function __construct(
        ContentManagerPageTypeService $pageTypeService,
        LanguageService $languageService,
        CurrentThemeInterface $currentTheme,
        ContentManagerHelper $contentManagerHelper
    ) {
        parent::__construct($pageTypeService,
                            $languageService,
                            $currentTheme);
        
        $this->contentManagerHelper = $contentManagerHelper;
        $this->pageTypeService      = $pageTypeService;
    }
    
    
    /**
     * @inheritDoc
     */
    protected function update($contentManagerContent): void
    {
        $this->pageTypeService->updatePage($contentManagerContent);
    }
    
    
    /**
     * @inheritDoc
     */
    protected function create($contentManagerContent): void
    {
        $this->pageTypeService->createPage($contentManagerContent);
    }
    
    
    
    
    /**
     * @return \InfoPageContent
     * @throws \UnfinishedBuildException
     */
    protected function createContent(): \InfoPageContent
    {
        $dbContent = [];
        
        /** @var Language $language */
        foreach ($this->languageService->getActiveLanguages() as $language) {
            $dbContent[] = [
                'language_id'               => $language->id(),
                'languages_id'              => $language->id(),
                'content_name'              => $this->option->value($language)['contentName'],
                'content_title'             => $this->option->value($language)['contentTitle'],
                'content_heading'           => $this->option->value($language)['contentHeading'],
                'content_text'              => $this->option->value($language)['contentText'],
                'download_file'             => $this->option->value($language)['downloadFile'],
                'contents_meta_title'       => $this->option->value($language)['contentsMetaTitle'],
                'contents_meta_keywords'    => $this->option->value($language)['contentsMetaKeywords'],
                'contents_meta_description' => $this->option->value($language)['contentsMetaDescription'],
                'gm_priority'               => $this->option->value($language)['gmPriority'],
                'gm_sitemap_entry'          => $this->option->value($language)['gmSitemapEntry'],
                'gm_changefreq'             => $this->option->value($language)['gmChangeFreq'],
                'opengraph_image'           => $this->option->value($language)['opengraphImage'],
                'gm_robots_entry'           => $this->option->value($language)['gmRobotsEntry'],
                'content_status'            => $this->option->value($language)['contentStatus'],
                'sort_order'                => $this->option->sortOrder(),
                'content_delete'            => $this->option->deletable() ? '1' : '0',
                'content_position'          => $this->option->value($language)['contentPosition'],
                'gm_url_keywords'           => $this->contentManagerHelper->clearUrlKeywords($this->option->value($language)['gmUrlKeywords']),
                'rewrite_url'               => $this->contentManagerHelper->sanitizeUrl($this->option->value($language)['urlRewrite']),
            ];
        }
        
        $names            = $this->contentValueObjectFactory->createContentNameCollection($dbContent);
        $titles           = $this->contentValueObjectFactory->createContentTitleCollection($dbContent);
        $headings         = $this->contentValueObjectFactory->createContentHeadingCollection($dbContent);
        $texts            = $this->contentValueObjectFactory->createContentTextCollection($dbContent);
        $downloads        = $this->contentValueObjectFactory->createDownloadFileCollection($dbContent);
        $metaTitles       = $this->contentValueObjectFactory->createContentMetaTitleCollection($dbContent);
        $metaKeywords     = $this->contentValueObjectFactory->createContentMetaKeywordsCollection($dbContent);
        $metaDescriptions = $this->contentValueObjectFactory->createContentMetaDescriptionCollection($dbContent);
        $urlKeywords      = $this->contentValueObjectFactory->createContentUrlKeywordsCollection($dbContent);
        $urlRewrites      = $this->contentValueObjectFactory->createContentUrlRewriteCollection($dbContent);
        $sitemaps         = $this->contentValueObjectFactory->createContentSitemapCollection($dbContent);
        $openGraphs       = $this->contentValueObjectFactory->createContentOpenGraphImageCollection($dbContent);
        $allowRobots      = $this->contentValueObjectFactory->createContentAllowRobotsStatusCollection($dbContent);
        $contentStatuses  = $this->contentValueObjectFactory->createContentStatusCollection($dbContent);
        $sortOrders       = $this->contentValueObjectFactory->createContentSortOrder($dbContent);
        $deletable        = $this->contentValueObjectFactory->createContentDelete($dbContent);
        
        if ($this->option->contentIdentification() === null) {
            /**
             * @var ContentIdentificationInterface $identification
             */
            $this->option->setContentIdentification($this->contentReadService->nextContentGroupId());
        }
        
        if (empty($this->option->contentIdentification()->contentAlias())) {
            //create an ID with the currentTheme
            $this->option->setContentIdentification($this->option->contentIdentification()
                                                        ->forTheme($this->currentTheme->id()));
        }
        
        return InfoPageContentBuilder::create()
            ->usingTitles($titles)
            ->usingTexts($texts)
            ->usingStatus($contentStatuses)
            ->usingId($this->option->contentIdentification())
            ->usingHeadings($headings)
            ->inPosition($this->pageTypeService->createPagePositionFromString(current($dbContent)['content_position']))
            ->usingAllowRobotsStatuses($allowRobots)
            ->usingDownloadFiles($downloads)
            ->usingMetaDescriptions($metaDescriptions)
            ->usingMetaKeywords($metaKeywords)
            ->usingMetaTitles($metaTitles)
            ->usingNames($names)
            ->usingOpengraphImages($openGraphs)
            ->usingSitemaps($sitemaps)
            ->usingUrlKeywords($urlKeywords)
            ->usingUrlRewrites($urlRewrites)
            ->usingSortOrder($sortOrders)
            ->usingDelete($deletable)
            ->build();
    }
}
