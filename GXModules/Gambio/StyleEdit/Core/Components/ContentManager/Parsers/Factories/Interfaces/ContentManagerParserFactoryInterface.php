<?php
/*--------------------------------------------------------------------------------------------------
    ContentManagerParseFactoryInterface.php 2021-02-17
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2021 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\StyleEdit\Core\Components\ContentManager\Parsers\Factories\Interfaces;

use Gambio\StyleEdit\Core\Components\ContentManager\Parsers\Interfaces\ContentManagerParserInterface;
use Gambio\StyleEdit\Core\Components\ContentManager\Parsers\Exceptions\ParserNotFoundException;

interface ContentManagerParserFactoryInterface
{
    /**
     * @param $content
     *
     * @return ContentManagerParserInterface
     *
     * @throws ParserNotFoundException
     */
    public function createParserFor($content): ContentManagerParserInterface;
}
