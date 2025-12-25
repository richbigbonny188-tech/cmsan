<?php
/* --------------------------------------------------------------
   TextPhraseRepository.php 2021-05-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\TextManager\App\Data;

use Gambio\Core\Cache\Services\CacheFactory;
use Gambio\Core\Cache\Services\SafeCache;

/**
 * Class TextPhraseRepository
 *
 * @package Gambio\Core\TextManager\App\Data
 */
class TextPhraseRepository implements \Gambio\Core\TextManager\Services\TextPhraseRepository
{
    /**
     * @var SafeCache
     */
    private $textCache;
    
    /**
     * @var TextPhraseReader
     */
    private $reader;
    
    
    /**
     * TextPhraseRepository constructor.
     *
     * @param CacheFactory     $cacheFactory
     * @param TextPhraseReader $reader
     */
    public function __construct(CacheFactory $cacheFactory, TextPhraseReader $reader)
    {
        $this->textCache = $cacheFactory->createCacheFor('text_cache');
        $this->reader    = $reader;
    }
    
    
    /**
     * @inheritDoc
     */
    public function getSectionPhrases(string $section, int $languageId): array
    {
        $cacheKey = $section . '_lang_' . $languageId;
        if ($this->textCache->has($cacheKey)) {
            return $this->textCache->get($cacheKey, []);
        }
        
        $textPhrases = $this->reader->getSectionPhrases($section, $languageId);
        $this->textCache->set($cacheKey, $textPhrases);
        
        return $textPhrases;
    }
    
    
    /**
     * @inheritDoc
     */
    public function getPhraseText(string $phrase, string $section, int $languageId): string
    {
        $textPhrases = $this->getSectionPhrases($section, $languageId);
        
        return array_key_exists($phrase, $textPhrases) ? $textPhrases[$phrase] : $phrase;
    }
}