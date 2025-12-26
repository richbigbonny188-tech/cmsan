<?php
/* --------------------------------------------------------------
   TextManager.php 2021-05-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\TextManager\Services;

/**
 * Interface TextManager
 *
 * @package Gambio\Core\TextManager\Services
 */
interface TextManager
{
    /**
     * Returns a list with translated texts of all phrases in the given section.
     * If no language id is provided, the default one will be used.
     *
     * @param string   $section
     * @param int|null $languageId
     *
     * @return string[]
     */
    public function getSectionPhrases(string $section, int $languageId = null): array;
    
    
    /**
     * Returns a translated text for the given phrase and section.
     * If no language id is provided, the default one will be used.
     *
     * @param string   $phrase
     * @param string   $section
     * @param int|null $languageId
     *
     * @return string
     */
    public function getPhraseText(string $phrase, string $section, int $languageId = null): string;
}