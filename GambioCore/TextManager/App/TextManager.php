<?php
/* --------------------------------------------------------------
   TextManager.php 2021-05-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\TextManager\App;

use Gambio\Core\Application\ValueObjects\UserPreferences;
use Gambio\Core\TextManager\Services\TextPhraseRepository;

/**
 * Class TextManager
 *
 * @package Gambio\Core\TextManager\App
 */
class TextManager implements \Gambio\Core\TextManager\Services\TextManager
{
    /**
     * @var TextPhraseRepository
     */
    private $repository;
    
    /**
     * @var int
     */
    private $defaultLanguageId;
    
    
    /**
     * TextManager constructor.
     *
     * @param TextPhraseRepository $repository
     * @param UserPreferences      $userPreferences
     */
    public function __construct(TextPhraseRepository $repository, UserPreferences $userPreferences)
    {
        $this->repository        = $repository;
        $this->defaultLanguageId = $userPreferences->languageId();
    }
    
    
    /**
     * @inheritDoc
     */
    public function getSectionPhrases(string $section, int $languageId = null): array
    {
        $languageId = $languageId ?? $this->defaultLanguageId;
        
        return $this->repository->getSectionPhrases($section, $languageId);
    }
    
    
    /**
     * @inheritDoc
     */
    public function getPhraseText(string $phrase, string $section, int $languageId = null): string
    {
        $languageId = $languageId ?? $this->defaultLanguageId;
        
        return $this->repository->getPhraseText($phrase, $section, $languageId);
    }
}