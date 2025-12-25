<?php
/*--------------------------------------------------------------------
 gm_get_content.inc.php 2020-2-11
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

use Gambio\Admin\Modules\Language\Model\Exceptions\LanguageNotFoundException;
use Gambio\Core\Configuration\Services\ConfigurationService;
use Gambio\Core\Language\Services\LanguageService;

/**
 * Fetches configuration values by the given key and language id.
 * Returns null if no configuration item was found by provided key.
 *
 * @param string|array $gm_key
 * @param string|int   $languages_id
 * @param string       $result_type
 *
 * @return array|string|null
 * @deprecated This method shouldn't be used for new domains and exist for compatibility purposes.
 */
function gm_get_content($gm_key, $languages_id, $result_type = 'ASSOC')
{
    /** @var ConfigurationService $configurationService */
    /** @var LanguageService $languageService */
    $container            = LegacyDependencyContainer::getInstance();
    $configurationService = $container->get(ConfigurationService::class);
    $languageService      = $container->get(LanguageService::class);
    $prefix               = 'gm_configuration/';
    try {
        $language     = $languageService->getLanguageById($languages_id);
        $languageCode = $language->code();
    } catch (LanguageNotFoundException $e) {
        // fallback language code
        $languageCode = 'de';
    }
    
    if (!is_array($gm_key)) {
        $gmKey = "{$prefix}{$gm_key}";
        $item  = $configurationService->findLanguageDependent($gmKey, $languageCode);
        
        if ($item) {
            return $item->value();
        }
        // fallback without legacy key, eg. for hub configuration values
        $item = $configurationService->findLanguageDependent($gm_key, $languageCode);
        
        return $item ? $item->value() : null;
    }
    
    $values = [];
    if ($result_type === 'ASSOC') {
        foreach ($gm_key as $key) {
            $gmKey = "{$prefix}{$key}";
            $item  = $configurationService->findLanguageDependent($gmKey, $languageCode);
            if ($item) {
                // keep without prefix for compatibility reasons
                $values[$key] = $item->value();
            }
        }
    }
    if ($result_type === 'NUMERIC') {
        foreach ($gm_key as $key) {
            $gmKey = "{$prefix}{$key}";
            $item  = $configurationService->findLanguageDependent($gmKey, $languageCode);
            if ($item) {
                $values[] = $item->value();
            }
        }
    }
    
    return count($values) > 0 ? $values : null;
}
