<?php
/* --------------------------------------------------------------
   LanguageService.php 2021-05-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\Language\App;

use Gambio\Admin\Modules\Language\Model\Collections\Languages;
use Gambio\Admin\Modules\Language\Model\Language;
use Gambio\Admin\Modules\Language\Services\LanguageFilterService;
use Gambio\Admin\Modules\Language\Services\LanguageReadService;

/**
 * Class LanguageService
 *
 * @package Gambio\Core\Language\App
 */
class LanguageService implements \Gambio\Core\Language\Services\LanguageService
{
    /**
     * @var LanguageReadService
     */
    private $adminLanguageReadService;
    
    /**
     * @var LanguageFilterService
     */
    private $adminLanguageFilterService;
    
    
    /**
     * LanguageService constructor.
     *
     * @param LanguageReadService   $adminLanguageReadService
     * @param LanguageFilterService $adminLanguageFilterService
     */
    public function __construct(
        LanguageReadService   $adminLanguageReadService,
        LanguageFilterService $adminLanguageFilterService
    ) {
        $this->adminLanguageReadService   = $adminLanguageReadService;
        $this->adminLanguageFilterService = $adminLanguageFilterService;
    }
    
    
    /**
     * @inheritDoc
     */
    public function getAvailableLanguages(): Languages
    {
        $filters = ['status' => '1'];
        $sorting = 'sortOrder';
        
        return $this->adminLanguageFilterService->filterLanguages($filters, $sorting);
    }
    
    
    /**
     * @inheritDoc
     */
    public function getAvailableAdminLanguages(): Languages
    {
        $filters = ['statusAdmin' => '1'];
        $sorting = 'sortOrder';
        
        return $this->adminLanguageFilterService->filterLanguages($filters, $sorting);
    }
    
    
    /**
     * @inheritDoc
     */
    public function getLanguageById(int $id): Language
    {
        return $this->adminLanguageReadService->getLanguageById($id);
    }
    
    
    /**
     * @inheritDoc
     */
    public function getLanguageByCode(string $code): Language
    {
        return $this->adminLanguageReadService->getLanguageByCode($code);
    }
}