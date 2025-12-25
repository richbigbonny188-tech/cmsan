<?php
/* --------------------------------------------------------------
   EscapeXmlTrait.php 2023-01-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace GXModules\Gambio\Afterbuy\OrderExport\Model\Request;

/**
 * Trait EscapeXmlTrait
 *
 * @package GXModules\Gambio\Afterbuy\OrderExport\Model\Request
 */
trait EscapeXmlTrait
{
    /**
     * Escapes the given string to be safe to be used as xml value.
     *
     * @param string|null $string
     *
     * @return string|null
     */
    private function escapeForXml(?string $string): ?string
    {
        if ($string === null) {
            return null;
        }
        return htmlspecialchars($string, ENT_XML1, 'UTF-8');
    }
}