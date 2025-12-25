<?php
/* --------------------------------------------------------------
   get_locale_by_language_id.inc.php 2020-10-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

use Gambio\Admin\Modules\Language\Model\Exceptions\LanguageNotFoundException;
use Gambio\Core\Language\Services\LanguageService;

function get_locale_by_language_id(int $languageId): ?array
{
    static $locales;

    if ($locales === null) {
        $locales = [];
    }

    if (array_key_exists($languageId, $locales)) {
        return $locales[$languageId];
    }

    /** @var LanguageService $languageService */
    $languageService = LegacyDependencyContainer::getInstance()->get(LanguageService::class);

    try {
        $languageId = $languageId > 0? $languageId : 2;
        $languageFile = DIR_FS_CATALOG . 'lang/' . $languageService->getLanguageById($languageId)->directory()
                        . '/init.inc.php';

        if (file_exists($languageFile)) {
            $languageFileContent = file_get_contents($languageFile);

            preg_match('/.*setlocale\(\s*LC_TIME\s*,\s*([^)]+)\).*/s', $languageFileContent, $matches);

            $localeParams = $matches[1] ?? null;
            if ($localeParams === null) {
                return $locales[$languageId] = null;
            }

            $localeParams = preg_replace('/[\'"\s]/', '', $localeParams);

            return $locales[$languageId] = explode(',', $localeParams);
        }
    } catch (LanguageNotFoundException $e) {
        return $locales[$languageId] = null;
    }

    return $locales[$languageId] = null;
}