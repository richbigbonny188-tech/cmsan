<?php
/*--------------------------------------------------------------------------------------------------
    ContentManagerParserInterface.php 2021-02-17
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2021 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\StyleEdit\Core\Components\ContentManager\Parsers\Interfaces;

/**
 * Interface ContentManagerParserInterface
 *
 * @package Gambio\StyleEdit\Parsers\Interfaces
 */
interface ContentManagerParserInterface
{
    /**
     * @return array
     */
    public function parse(): array;
}
