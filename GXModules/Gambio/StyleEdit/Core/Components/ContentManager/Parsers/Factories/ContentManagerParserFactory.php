<?php
/*--------------------------------------------------------------------------------------------------
    ContentManagerParseFactory.php 2021-08-19
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2021 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\StyleEdit\Core\Components\ContentManager\Parsers\Factories;

use Gambio\StyleEdit\Core\Components\ContentManager\Parsers\ContentManagerContentPageParser;
use Gambio\StyleEdit\Core\Components\ContentManager\Parsers\ContentManagerFileParser;
use Gambio\StyleEdit\Core\Components\ContentManager\Parsers\ContentManagerLinkParser;
use Gambio\StyleEdit\Core\Components\ContentManager\Parsers\Factories\Interfaces\ContentManagerParserFactoryInterface;
use Gambio\StyleEdit\Core\Components\ContentManager\Parsers\Interfaces\ContentManagerParserInterface;
use Gambio\StyleEdit\Core\Components\ContentManager\Parsers\Exceptions\ParserNotFoundException;
use InfoPageContent;
use LinkPageContent;
use MainFactory;
use PagesLinkProvider;
use ScriptPageContent;

class ContentManagerParserFactory implements ContentManagerParserFactoryInterface
{
    /**
     * @inheritDoc
     */
    public function createParserFor($content): ContentManagerParserInterface
    {
        if ($content instanceof InfoPageContent) {
            return new ContentManagerContentPageParser(new PagesLinkProvider(MainFactory::create_object('GMSEOBoost', [], true)), $content);
        }
        
        if ($content instanceof LinkPageContent) {
            return new ContentManagerLinkParser(new PagesLinkProvider(MainFactory::create_object('GMSEOBoost', [], true)), $content);
        }
        
        if ($content instanceof ScriptPageContent) {
            return new ContentManagerFileParser(new PagesLinkProvider(MainFactory::create_object('GMSEOBoost', [], true)), $content);
        }
    
        throw new ParserNotFoundException("Parser not found");
    }
}
