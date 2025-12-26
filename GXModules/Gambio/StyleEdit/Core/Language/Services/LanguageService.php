<?php
/*--------------------------------------------------------------------------------------------------
    LanguageService.php 2019-10-23
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2019 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\StyleEdit\Core\Language\Services;

use Exception;
use Gambio\StyleEdit\Adapters\Interfaces\LanguageServiceAdapterInterface;
use Gambio\StyleEdit\Adapters\LanguageServiceAdapter;
use Gambio\StyleEdit\Core\BuildStrategies\Interfaces\SingletonStrategyInterface;
use Gambio\StyleEdit\Core\Language\Entities\Language;
use Gambio\StyleEdit\Core\Language\Entities\LanguageCollection;

/**
 * Class LanguageService
 * @package Gambio\StyleEdit\Core\Language\Services
 */
class LanguageService implements SingletonStrategyInterface
{
    protected $activeLanguages;
    /**
     * @var LanguageServiceAdapter
     */
    protected $adapter;
    
    
    /**
     * LanguageService constructor.
     *
     * @param LanguageServiceAdapterInterface $adapter
     */
    public function __construct(LanguageServiceAdapterInterface $adapter = null)
    {
        $this->adapter = $adapter;
    }
    
    
    /**
     * @param string $content
     *
     * @return string
     * @throws Exception
     */
    public function translate(string $content): string
    {
        return $this->adapter ? $this->adapter->translate($content) : $content;
    }
    
    
    /**
     * @param string $languageCode
     *
     * @return Language
     */
    public function getByCode(string $languageCode): ?Language
    {
        if ($this->adapter !== null) {
            $languageId = $this->adapter->getIdLanguageByCode($languageCode);
            return new Language($languageCode, $languageId);
        }
        
        return null;
    }
    
    
    /**
     * @return LanguageCollection
     * @throws Exception
     */
    public function getActiveLanguages(): LanguageCollection
    {
        if ($this->activeLanguages === null && $this->adapter !== null) {
            /**
             * @var LanguageCollection $languageCollection
             */
            $this->activeLanguages = new LanguageCollection();
            if ($this->adapter) {
                foreach ($this->adapter->languages() as $id => $code) {
                    $this->activeLanguages->add(new Language($code, $id));
                }
            }
            
        }
        
        return $this->activeLanguages;
    }
    
    
}