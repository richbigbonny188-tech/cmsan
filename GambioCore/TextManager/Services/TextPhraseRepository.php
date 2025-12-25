<?php
/* --------------------------------------------------------------
   TextPhraseRepository.php 2020-04-16
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
 * Interface TextPhraseRepository
 * @package Gambio\Core\TextManager\Services
 */
interface TextPhraseRepository
{
    /**
     * Returns a list with translated texts of all phrases in the given section.
     *
     * @param string $section
     * @param int    $languageId
     *
     * @return string[]
     */
    public function getSectionPhrases(string $section, int $languageId): array;
    
    
    /**
     * Returns a translated text for the given phrase and section.
     *
     * @param string $phrase
     * @param string $section
     * @param int    $languageId
     *
     * @return string
     */
    public function getPhraseText(string $phrase, string $section, int $languageId): string;
}