<?php
/*--------------------------------------------------------------------------------------------------
    ContentManagerFileParser.php 2021-08-19
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2021 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\StyleEdit\Core\Components\ContentManager\Parsers;

use ContentInterface;
use PagesLinkProvider;

/**
 * Class ContentManagerFileParser
 * @package Gambio\StyleEdit\Parsers
 */
class ContentManagerFileParser extends AbstractContentManagerParser
{
    /**
     * @var \ScriptPageContent
     */
    protected $content;
    
    
    /**
     * ContentManagerFileParser constructor.
     *
     * @param PagesLinkProvider $pagesLinkProvider
     * @param \ScriptPageContent  $content
     */
    public function __construct(PagesLinkProvider $pagesLinkProvider, \ScriptPageContent $content)
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
            'content'             => $this->parseScriptContent($content),
        ];
    }
    
    
    private function parseScriptContent(\ScriptPageContent $contentScript): array
    {
        $content = [];
    
        if ($contentScript->names()) {
            /** @var \ContentName $name */
            foreach ($contentScript->names() as $name) {
                $languageCode = strtolower($name->languageCode());
            
                if (!isset($content[$languageCode])) {
                    $content[$languageCode] = [];
                }
            
                $content[$languageCode]['contentName'] = $name->content();
            }
        }
    
        if ($contentScript->titles()) {
            /** @var \ContentTitle $title */
            foreach ($contentScript->titles() as $title) {
                $languageCode = strtolower($title->languageCode());
            
                if (!isset($content[$languageCode])) {
                    $content[$languageCode] = [];
                }
            
                $content[$languageCode]['contentTitle'] = $title->content();
            }
        }
    
    
        if ($contentScript->status()) {
            /** @var \ContentStatus $status */
            foreach ($contentScript->status() as $status) {
                $languageCode = strtolower($status->languageCode());
            
                if (!isset($content[$languageCode])) {
                    $content[$languageCode] = [];
                }
            
                $content[$languageCode]['contentStatus'] = (int)$status->content() === 1;
            }
        }
    
        if ($contentScript->scripts()) {
            /** @var \ContentScriptFile $script */
            foreach ($contentScript->scripts() as $script) {
                $languageCode = strtolower($script->languageCode());
            
                if (!isset($content[$languageCode])) {
                    $content[$languageCode] = [];
                }
            
                $content[$languageCode]['contentScript'] = $script->content();
            }
        }
    
        if ($contentScript->openInNewTab()) {
            /** @var \ContentOpenInNewTabStatus $status */
            foreach ($contentScript->openInNewTab() as $status) {
                $languageCode = strtolower($status->languageCode());
            
                if (!isset($content[$languageCode])) {
                    $content[$languageCode] = [];
                }
            
                $content[$languageCode]['opensInNewTabStatus'] = $status->opensInNewTab();
            }
        }
    
        if ($content) {
            foreach ($content as &$language) {
                $language['contentPosition'] = $contentScript->position();
            }
            
            unset($language);
        }
    
    
        return $content;
    }
}
