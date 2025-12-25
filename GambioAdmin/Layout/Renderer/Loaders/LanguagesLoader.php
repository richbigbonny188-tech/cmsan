<?php
/* --------------------------------------------------------------
   LanguagesLoader.php 2020-10-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Layout\Renderer\Loaders;

use Gambio\Admin\Modules\Language\Model\Exceptions\LanguageNotFoundException;
use Gambio\Admin\Modules\Language\Model\Language;
use Gambio\Core\Application\ValueObjects\UserPreferences;
use Gambio\Core\Language\Services\LanguageService;
use Gambio\Core\TemplateEngine\LayoutData;
use Gambio\Core\TemplateEngine\Loader;

/**
 * Class LanguagesLoader
 *
 * @package Gambio\Admin\Layout\Renderer\Loaders
 */
class LanguagesLoader implements Loader
{
    /**
     * @var LanguageService
     */
    private $service;
    
    /**
     * @var UserPreferences
     */
    private $userPreferences;
    
    
    /**
     * LanguagesLoader constructor.
     *
     * @param LanguageService $repository
     * @param UserPreferences $userPreferences
     */
    public function __construct(LanguageService $repository, UserPreferences $userPreferences)
    {
        $this->service         = $repository;
        $this->userPreferences = $userPreferences;
    }
    
    
    /**
     * @inheritDoc
     * @throws LanguageNotFoundException
     */
    public function load(LayoutData $data): void
    {
        $currentLanguageCode = $this->service->getLanguageById($this->userPreferences->languageId())->code();
        
        $data->assign('languageCode', $currentLanguageCode);
        $data->assign('adminLanguages', $this->getAvailableAdminLanguagesData());
        $data->assign('languages', $this->getAvailableLanguagesData());
    }
    
    
    /**
     * @return array
     */
    private function getAvailableLanguagesData(): array
    {
        $languagesData = [];
        
        /** @var Language $language */
        foreach ($this->service->getAvailableLanguages() as $language) {
            $languagesData[] = [
                'id'   => $language->id(),
                'code' => $language->code(),
                'name' => $language->name(),
            ];
        }
        
        return $languagesData;
    }
    
    
    /**
     * @return array
     */
    private function getAvailableAdminLanguagesData(): array
    {
        $languagesData = [];
        
        /** @var Language $language */
        foreach ($this->service->getAvailableAdminLanguages() as $language) {
            $languagesData[] = [
                'id'   => $language->id(),
                'code' => $language->code(),
                'name' => $language->name(),
            ];
        }
        
        return $languagesData;
    }
}