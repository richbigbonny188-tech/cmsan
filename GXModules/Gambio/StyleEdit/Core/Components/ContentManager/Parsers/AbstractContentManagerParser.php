<?php
/*--------------------------------------------------------------------------------------------------
    AbstractContentManagerParser.php 2021-07-19
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2021 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\StyleEdit\Core\Components\ContentManager\Parsers;

use Gambio\StyleEdit\Core\Components\ContentManager\Parsers\Interfaces\ContentManagerParserInterface;

/**
 * Class AbstractContentManagerParser
 *
 * @package Gambio\StyleEdit\Parsers
 */
abstract class AbstractContentManagerParser implements ContentManagerParserInterface
{
    /**
     * @var \PagesLinkProvider
     */
    protected $pagesLinkProvider;
    
    /**
     * @var \ContentInterface
     */
    protected $content;
    
    
    public function __construct(\PagesLinkProvider $pagesLinkProvider)
    {
        $this->pagesLinkProvider = $pagesLinkProvider;
    }
    
    
    /**
     * @param \ContentInterface $content
     *
     * @return string
     */
    protected function getContentPagePublicLink(\ContentInterface $content): string
    {
        return $this->pagesLinkProvider->getPageContentLink($content->id()->contentGroup());
    }
    
    
    abstract public function parse(): array;
    
    
    /**
     * @return int|null
     */
    protected function getContentGroup(): ?int
    {
        /** @var \ContentIdentificationInterface $contentId */
        $contentId = $this->content->id();
        
        return $contentId ? $contentId->contentGroup() : null;
    }
    
    
    /**
     * @return string|null
     */
    protected function getContentAlias(): ?string
    {
        /** @var \ContentIdentificationInterface $contentId */
        $contentId = $this->content->id();
        
        return $contentId ? $contentId->contentAlias() : null;
    }
}
