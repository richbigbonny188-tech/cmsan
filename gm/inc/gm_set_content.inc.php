<?php
/*--------------------------------------------------------------------
 gm_set_content.inc.php 2020-2-11
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

/*
    -> function to set content values
*/

use Gambio\Admin\Modules\Language\Model\Exceptions\LanguageNotFoundException;
use Gambio\Core\Configuration\Services\ConfigurationService;
use Gambio\Core\Language\Services\LanguageService;

/**
 * @param      $gm_key
 * @param      $gm_value
 * @param      $languages_id
 * @param int  $group_id
 * @param bool $doTrimming
 *
 * @deprecated
 */
function gm_set_content($gm_key, $gm_value, $languages_id, $group_id = 0, $doTrimming = true)
{
    /** @var ConfigurationService $configurationService */
    /** @var LanguageService $languageService */
    $container            = LegacyDependencyContainer::getInstance();
    $configurationService = $container->get(ConfigurationService::class);
    $languageService      = $container->get(LanguageService::class);
    
    $prefix = "gm_configuration/";
    $key = strpos($gm_key, $prefix) !== 0 ? "{$prefix}{$gm_key}" : $gm_key;
    
    try {
        $languageCode = $languageService->getLanguageById($languages_id)->code();
    } catch (LanguageNotFoundException $e) {
        $languageCode = 'de';
    }
    
    $configurationService->saveLanguageDependent($key, $languageCode, $gm_value);
}