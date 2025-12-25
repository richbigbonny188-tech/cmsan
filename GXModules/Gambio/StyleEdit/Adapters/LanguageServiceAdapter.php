<?php
/*--------------------------------------------------------------------------------------------------
    LanguageServiceAdapter.php 2020-02-03
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\StyleEdit\Adapters;

use Exception;
use Gambio\StyleEdit\Adapters\Interfaces\LanguageServiceAdapterInterface;
use Gambio\StyleEdit\Core\Language\Entities\Language;
use Gambio\StyleEdit\Core\SingletonPrototype;
use IdType;
use LanguageCode;
use LanguageProvider;
use LanguageService;
use MainFactory;
use StringType;

/**
 * Class LanguageServiceAdapter
 * @package Gambio\StyleEdit\Adapters
 * @codeCoverageIgnore
 */
class LanguageServiceAdapter implements LanguageServiceAdapterInterface
{
    
    
    /**
     * @var LanguageProvider
     */
    protected $provider;
    /**
     * @var LanguageService
     */
    protected $service;
    
    
    /**
     * LanguageServiceAdapter constructor.
     *
     * @param LanguageProvider $provider
     */
    public function __construct(LanguageProvider $provider)
    {
        $this->provider = $provider;
    }
    
    
    /**
     * must return an array with LanguageId=>LanguageInitials like [1 =>'en']
     * @return array
     */
    public function languages(): array
    {
        $result    = [];
        $languages = $this->provider->getActiveCodes();
    
        foreach ($languages->getArray() as $value) {
            /**
             * @var LanguageCode $value
             */
            $result[$this->provider->getIdByCode($value)] = $value->asString();
        }
        $languages = $this->provider->getAdminCodes();
    
        foreach ($languages->getArray() as $value) {
            /**
             * @var LanguageCode $value
             */
            $result[$this->provider->getIdByCode($value)] = $value->asString();
        }
    
        return $result;
    }
    
    
    /**
     * @param string $languageCode
     *
     * @return int
     */
    public function getIdLanguageByCode(string $languageCode): int
    {
        return $this->provider->getIdByCode(new LanguageCode(new StringType($languageCode)));
    }
    
    
    /**
     * @param string $content
     *
     * @return string
     * @throws Exception
     */
    public function translate(string $content): string
    {
        return $this->service()->translate($content);
    }
    
    
    /**
     * @return LanguageService
     * @throws Exception
     */
    protected function service(): LanguageService
    {
        if ($this->service === null) {
            /**
             * @var Language $activeLanguage
             */
            $activeLanguage = SingletonPrototype::instance()->get(Language::class);
            $this->service  = MainFactory::create(\LanguageService::class, new IdType($activeLanguage->id()));
        }
        
        return $this->service;
    }
}