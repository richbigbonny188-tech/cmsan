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

namespace Gambio\Core\Language\Services;

use Gambio\Admin\Modules\Language\Model\Collections\Languages;
use Gambio\Admin\Modules\Language\Model\Exceptions\LanguageNotFoundException;
use Gambio\Admin\Modules\Language\Model\Language;

/**
 * Interface LanguageService
 *
 * @package Gambio\Core\Language\Services
 */
interface LanguageService
{
    /**
     * Returns a collection of all available languages.
     *
     * @return Languages
     */
    public function getAvailableLanguages(): Languages;
    
    
    /**
     * Returns a collection of available admin languages.
     *
     * @return Languages
     */
    public function getAvailableAdminLanguages(): Languages;
    
    
    /**
     * Returns language information of the language with the given language id.
     *
     * @param int $id
     *
     * @return Language
     * @throws LanguageNotFoundException
     */
    public function getLanguageById(int $id): Language;
    
    
    /**
     * Returns language information of the language with the given language code.
     *
     * @param string $code
     *
     * @return Language
     * @throws LanguageNotFoundException
     */
    public function getLanguageByCode(string $code): Language;
}