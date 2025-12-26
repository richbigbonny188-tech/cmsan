<?php
/*--------------------------------------------------------------------------------------------------
    ContentManagerLinkParser.php 2021-08-19
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2021 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\StyleEdit\Core\Components\ContentManager\Parsers;

use LinkPageContent;
use PagesLinkProvider;

/**
 * Class ContentManagerLinkParser
 * @package Gambio\StyleEdit\Parsers
 */
class ContentManagerLinkParser extends AbstractContentManagerParser
{
    /**
     * @var LinkPageContent
     */
    protected $content;
    
    
    /**
     * ContentManagerLinkParser constructor.
     *
     * @param PagesLinkProvider $pagesLinkProvider
     * @param LinkPageContent   $content
     */
    public function __construct(PagesLinkProvider $pagesLinkProvider, LinkPageContent $content)
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
            'id'                  => $this->getContentGroup(),
            'contentGroup'        => $this->getContentGroup(),
            'contentAlias'        => $this->getContentAlias(),
            'sortOrder'           => $content->order(),
            'deletable'           => $content->isDeletable(),
            'position'            => $content->position(),
            'pageType'            => $content->type(),
            'publicLink'          => $this->getContentPagePublicLink($content),
            'content'             => $this->parseLinkContent($content),
        ];
    }
    
    
    /**
     * Parses the link content from Gambio to StyleEdit 4
     *
     * @param LinkPageContent $contentLink
     *
     * @return array
     */
    protected function parseLinkContent(LinkPageContent $contentLink): array
    {
        $content = [];
    
        if ($contentLink->names()) {
            /** @var \ContentName $name */
            foreach ($contentLink->names() as $name) {
                $languageCode = strtolower($name->languageCode());
                
                if (!isset($content[$languageCode])) {
                    $content[$languageCode] = [];
                }
                
                $content[$languageCode]['contentName'] = $name->content();
            }
        }
    
        if ($contentLink->titles()) {
            /** @var \ContentTitle $title */
            foreach ($contentLink->titles() as $title) {
                $languageCode = strtolower($title->languageCode());
                
                if (!isset($content[$languageCode])) {
                    $content[$languageCode] = [];
                }
                
                $content[$languageCode]['contentTitle'] = $title->content();
            }
        }
    
        if ($contentLink->links()) {
            /** @var \ContentLink $link */
            foreach ($contentLink->links() as $link) {
                $languageCode = strtolower($link->languageCode());
                
                if (!isset($content[$languageCode])) {
                    $content[$languageCode] = [];
                }
                
                $content[$languageCode]['contentLink'] = $link->content();
            }
        }
    
        if ($contentLink->status()) {
            /** @var \ContentStatus $status */
            foreach ($contentLink->status() as $status) {
                $languageCode = strtolower($status->languageCode());
                
                if (!isset($content[$languageCode])) {
                    $content[$languageCode] = [];
                }
                
                $content[$languageCode]['contentStatus'] = (int)$status->content() === 1;
            }
        }
    
        if ($contentLink->openInNewTab()) {
            /** @var \ContentOpenInNewTabStatus $status */
            foreach ($contentLink->openInNewTab() as $status) {
                $languageCode = strtolower($status->languageCode());
            
                if (!isset($content[$languageCode])) {
                    $content[$languageCode] = [];
                }
            
                $content[$languageCode]['opensInNewTabStatus'] = $status->opensInNewTab();
            }
        }
    
        if ($content) {
            foreach ($content as &$language) {
                $language['contentPosition'] = $contentLink->position();
            }
    
            unset($language);
        }
        
        return $content;
    }
}
