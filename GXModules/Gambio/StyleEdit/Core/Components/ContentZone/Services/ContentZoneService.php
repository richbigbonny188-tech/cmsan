<?php
/*--------------------------------------------------------------------------------------------------
    ContentZoneService.php 2019-10-24
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2019 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\StyleEdit\Core\Components\ContentZone\Services;

use FileNotFoundException;
use Gambio\StyleEdit\Core\Components\ContentZone\Entities\ContentZoneData;
use Gambio\StyleEdit\Core\Components\ContentZone\Entities\ContentZoneOption;
use Gambio\StyleEdit\Core\Components\ContentZone\Repositories\ContentZoneRepository;
use Gambio\StyleEdit\Core\Language\Entities\Language;
use Gambio\StyleEdit\Core\Language\Services\LanguageService;
use ReflectionException;

/**
 * Class ContentZoneService
 * @package Gambio\StyleEdit\Core\Components\ContentZone\Services
 */
class ContentZoneService
{
    /**
     * @var ContentZoneRepository
     */
    protected $contentZoneRepository;
    /**
     * @var LanguageService
     */
    protected $languageService;
    
    
    /**
     * ContentZoneService constructor.
     *
     * @param ContentZoneRepository $contentZoneRepository
     * @param LanguageService       $languageService
     */
    public function __construct(ContentZoneRepository $contentZoneRepository, LanguageService $languageService)
    {
        $this->contentZoneRepository = $contentZoneRepository;
        $this->languageService       = $languageService;
    }
    
    
    /**
     * @param ContentZoneOption $option
     *
     * @throws FileNotFoundException
     */
    public function save(ContentZoneOption $option): void
    {
        
        //inside the method whe have to call me persist method of the options
        $this->contentZoneRepository->saveContentZoneData($option);
        
        $activeLanguages = $this->languageService->getActiveLanguages();
        foreach ($activeLanguages as $language) {
            $this->contentZoneRepository->saveContentZoneTemplate($option, $language);
        }
    }
    
    
    /**
     * @return ContentZoneData
     * @throws FileNotFoundException
     * @throws ReflectionException
     */
    public function getAll(): ContentZoneData
    {
        return $this->contentZoneRepository->getAll();
    }
    
    
    /**
     * @param string $id
     *
     * @return ContentZoneOption
     * @throws FileNotFoundException
     */
    public function getById(string $id): ContentZoneOption
    {
        return $this->contentZoneRepository->getById($id);
    }
}
