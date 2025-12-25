<?php
/* --------------------------------------------------------------
   LanguageTypeFactory.php 2021-05-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Configuration\Services\TypeFactories;

use Gambio\Admin\Modules\Configuration\Model\Entities\Type;
use Gambio\Admin\Modules\Language\Model\Language;
use Gambio\Core\Language\Services\LanguageService;

/**
 * Class LanguageTypeFactory
 *
 * @package Gambio\Admin\Modules\Configuration\Services\TypeFactories
 */
class LanguageTypeFactory implements TypeFactory
{
    /**
     * @var LanguageService
     */
    private $languageService;
    
    
    /**
     * LanguageTypeFactory constructor.
     *
     * @param LanguageService $languageService
     */
    public function __construct(LanguageService $languageService)
    {
        $this->languageService = $languageService;
    }
    
    
    /**
     * @param array $params
     *
     * @return Type
     */
    public function createType(array $params): Type
    {
        $id              = (isset($params['multiSelect']) && $params['multiSelect']) ? 'multi-select' : 'dropdown';
        $params['items'] = $this->getLanguages($params['useCodeAsValue'] ?? false);
        unset($params['useCodeAsValue'], $params['multiSelect']);
        
        return Type::create($id, $params);
    }
    
    
    /**
     * @param bool $useCodeAsValue
     *
     * @return array
     */
    private function getLanguages(bool $useCodeAsValue): array
    {
        $languages = [];
        /** @var Language $language */
        foreach ($this->languageService->getAvailableAdminLanguages() as $language) {
            $languages[] = [
                'value' => $useCodeAsValue ? $language->code() : $language->id(),
                'text'  => $language->name(),
            ];
        }
        
        return $languages;
    }
}