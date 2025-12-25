<?php
/*--------------------------------------------------------------------------------------------------
    ContentManagerPageParser.php 2021-08-18
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2021 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\StyleEdit\Core\Components\ContentManager\Parsers;

use InfoPageContent;
use PagesLinkProvider;

/**
 * Class ContentManagerContentPageParser
 *
 * @package Gambio\StyleEdit\Parsers
 */
class ContentManagerContentPageParser extends AbstractContentManagerParser
{
    /**
     * @var InfoPageContent
     */
    protected $content;
    
    
    /**
     * ContentManagerContentPageParser constructor.
     *
     * @param PagesLinkProvider $pagesLinkProvider
     * @param InfoPageContent   $content
     */
    public function __construct(PagesLinkProvider $pagesLinkProvider, InfoPageContent $content)
    {
        parent::__construct($pagesLinkProvider);
        $this->content = $content;
    }
    
    
    /**
     * @inheritDoc
     */
    public function parse(): array
    {
        $content = $this->content;
        
        return [
            'id'           => $this->getContentGroup(),
            'contentGroup' => $this->getContentGroup(),
            'contentAlias' => $this->getContentAlias(),
            'sortOrder'    => $content->order(),
            'deletable'    => $content->isDeletable(),
            'pageType'     => $content->type(),
            'publicLink'   => $this->getContentPagePublicLink($content),
            'content'      => $this->parsePageContent($content),
        ];
    }
    
    
    /**
     * Parses the content from Gambio to StyleEdit 4 format
     *
     * @param InfoPageContent $contentPage
     *
     * @return array
     */
    protected function parsePageContent(InfoPageContent $contentPage): array
    {
        $content = [];
        
        if ($contentPage->names()) {
            /** @var \ContentName $name */
            foreach ($contentPage->names() as $name) {
                $languageCode = strtolower($name->languageCode());
                
                if (!isset($content[$languageCode])) {
                    $content[$languageCode] = [];
                }
                
                $content[$languageCode]['contentName'] = $name->content();
            }
        }
        
        if ($contentPage->titles()) {
            /** @var \ContentTitle $title */
            foreach ($contentPage->titles() as $title) {
                $languageCode = strtolower($title->languageCode());
                
                if (!isset($content[$languageCode])) {
                    $content[$languageCode] = [];
                }
                
                $content[$languageCode]['contentTitle'] = $title->content();
            }
        }
        
        if ($contentPage->headings()) {
            /** @var \ContentHeading $heading */
            foreach ($contentPage->headings() as $heading) {
                $languageCode = strtolower($heading->languageCode());
                
                if (!isset($content[$languageCode])) {
                    $content[$languageCode] = [];
                }
                
                $content[$languageCode]['contentHeading'] = $heading->content();
            }
        }
        
        if ($contentPage->texts()) {
            /** @var \ContentText $text */
            foreach ($contentPage->texts() as $text) {
                $languageCode = strtolower($text->languageCode());
                
                if (!isset($content[$languageCode])) {
                    $content[$languageCode] = [];
                }
                
                $content[$languageCode]['contentText'] = $text->content();
            }
        }
        
        if ($contentPage->downloads()) {
            /** @var \ContentDownloadFile $download */
            foreach ($contentPage->downloads() as $download) {
                $languageCode = strtolower($download->languageCode());
                
                if (!isset($content[$languageCode])) {
                    $content[$languageCode] = [];
                }
                
                $content[$languageCode]['downloadFile'] = $download->content();
            }
        }
        
        if ($contentPage->metaTitles()) {
            /** @var \ContentMetaTitle $metaTitle */
            foreach ($contentPage->metaTitles() as $metaTitle) {
                $languageCode = strtolower($metaTitle->languageCode());
                
                if (!isset($content[$languageCode])) {
                    $content[$languageCode] = [];
                }
                
                $content[$languageCode]['contentsMetaTitle'] = $metaTitle->content();
            }
        }
        
        if ($contentPage->metaKeywords()) {
            /** @var \ContentMetaKeywords $metaKeywords */
            foreach ($contentPage->metaKeywords() as $metaKeywords) {
                $languageCode = strtolower($metaKeywords->languageCode());
                
                if (!isset($content[$languageCode])) {
                    $content[$languageCode] = [];
                }
                
                $content[$languageCode]['contentsMetaKeywords'] = $metaKeywords->content();
            }
        }
        
        if ($contentPage->metaDescriptions()) {
            /** @var \ContentMetaDescription $metaDescription */
            foreach ($contentPage->metaDescriptions() as $metaDescription) {
                $languageCode = strtolower($metaDescription->languageCode());
                
                if (!isset($content[$languageCode])) {
                    $content[$languageCode] = [];
                }
                
                $content[$languageCode]['contentsMetaDescription'] = $metaDescription->content();
            }
        }
        
        if ($contentPage->urlKeywords()) {
            /** @var \ContentUrlKeywords $urlKeyworkds */
            foreach ($contentPage->urlKeywords() as $urlKeyworkds) {
                $languageCode = strtolower($urlKeyworkds->languageCode());
                
                if (!isset($content[$languageCode])) {
                    $content[$languageCode] = [];
                }
                
                $content[$languageCode]['gmUrlKeywords'] = $urlKeyworkds->content();
            }
        }
        
        if ($contentPage->urlRewrites()) {
            /** @var \ContentUrlRewrite $urlRewrite */
            foreach ($contentPage->urlRewrites() as $urlRewrite) {
                $languageCode = strtolower($urlRewrite->languageCode());
                
                if (!isset($content[$languageCode])) {
                    $content[$languageCode] = [];
                }
                
                $content[$languageCode]['urlRewrite'] = $urlRewrite->content();
            }
        }
        
        if ($contentPage->sitemaps()) {
            /** @var \ContentSitemap $sitemap */
            foreach ($contentPage->sitemaps() as $sitemap) {
                $languageCode = strtolower($sitemap->languageCode());
                
                if (!isset($content[$languageCode])) {
                    $content[$languageCode] = [];
                }
                
                $content[$languageCode]['gmPriority'] = $sitemap->priority();
                $content[$languageCode]['gmSitemapEntry'] = $sitemap->isVisible();
                $content[$languageCode]['gmChangeFreq'] = $sitemap->frequencyOfChange();
            }
        }
        
        if ($contentPage->opengraphImages()) {
            /** @var \ContentOpengraphImage $openGraphImage */
            foreach ($contentPage->opengraphImages() as $openGraphImage) {
                $languageCode = strtolower($openGraphImage->languageCode());
                
                if (!isset($content[$languageCode])) {
                    $content[$languageCode] = [];
                }
                
                $content[$languageCode]['opengraphImage'] = $openGraphImage->content();
            }
        }
        
        if ($contentPage->status()) {
            /** @var \ContentStatus $status */
            foreach ($contentPage->status() as $status) {
                $languageCode = strtolower($status->languageCode());
                
                if (!isset($content[$languageCode])) {
                    $content[$languageCode] = [];
                }
                
                $content[$languageCode]['contentStatus'] = (int)$status->content() === 1;
            }
        }
    
        if ($contentPage->allowRobotsStatuses()) {
            /** @var \ContentAllowRobotsStatus $status */
            foreach ($contentPage->allowRobotsStatuses() as $status) {
                $languageCode = strtolower($status->languageCode());
            
                if (!isset($content[$languageCode])) {
                    $content[$languageCode] = [];
                }
            
                $content[$languageCode]['gmRobotsEntry'] = $status->isAllowed();
            }
        }
        
        if ($content) {
            foreach ($content as &$language) {
                $language['contentPosition'] = $contentPage->position();
            }
            
            unset($language);
        }
        
        return $content;
    }
}
