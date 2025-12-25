<?php
/* --------------------------------------------------------------
   LanguageFactory.php 2020-11-23
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Language\Services;

use Gambio\Admin\Modules\Language\Model\Collections\LanguageIds;
use Gambio\Admin\Modules\Language\Model\Collections\Languages;
use Gambio\Admin\Modules\Language\Model\Language;
use Gambio\Admin\Modules\Language\Model\ValueObjects\LanguageCode;
use Gambio\Admin\Modules\Language\Model\ValueObjects\LanguageId;

/**
 * Class LanguageFactory
 *
 * @package Gambio\Admin\Modules\Language\Services
 */
class LanguageFactory
{
    /**
     * Creates and returns a language ID.
     *
     * @param int $id
     *
     * @return LanguageId
     */
    public function createLanguageId(int $id): LanguageId
    {
        return LanguageId::create($id);
    }
    
    
    /**
     * Creates and returns a collection of language IDs.
     *
     * @param LanguageId ...$ids
     *
     * @return LanguageIds
     */
    public function createLanguageIds(LanguageId ...$ids): LanguageIds
    {
        return LanguageIds::create(...$ids);
    }
    
    
    /**
     * Creates and returns a language code.
     *
     * @param string $code
     *
     * @return LanguageCode
     */
    public function createLanguageCode(string $code): LanguageCode
    {
        return LanguageCode::create($code);
    }
    
    
    /**
     * Creates and returns a language.
     *
     * @param int    $id
     * @param string $code
     * @param string $name
     * @param string $charset
     * @param string $directory
     *
     * @return Language
     */
    public function createLanguage(
        int $id,
        string $code,
        string $name,
        string $charset,
        string $directory
    ): Language {
        return Language::create($this->createLanguageId($id),
                                $this->createLanguageCode($code),
                                $name,
                                $charset,
                                $directory);
    }
    
    
    /**
     * Creates and returns a collection of languages.
     *
     * @param Language ...$parcelServices
     *
     * @return Languages
     */
    public function createLanguages(Language ...$parcelServices): Languages
    {
        return Languages::create(...$parcelServices);
    }
}