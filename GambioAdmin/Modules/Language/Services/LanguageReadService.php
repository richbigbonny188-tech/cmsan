<?php
/* --------------------------------------------------------------
   LanguageReadService.php 2020-11-23
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Language\Services;

use Gambio\Admin\Modules\Language\Model\Collections\Languages;
use Gambio\Admin\Modules\Language\Model\Exceptions\LanguageNotFoundException;
use Gambio\Admin\Modules\Language\Model\Language;

/**
 * Interface LanguageReadService
 *
 * @package Gambio\Admin\Modules\Language\Services
 */
interface LanguageReadService
{
    /**
     * Return all available languages.
     *
     * @return Languages
     */
    public function getLanguages(): Languages;
    
    
    /**
     * Returns a specific language by its ID.
     *
     * @param int $id
     *
     * @return Language
     *
     * @throws LanguageNotFoundException
     */
    public function getLanguageById(int $id): Language;
    
    
    /**
     * Returns a specific language by its two digit ISO code.
     *
     * @param string $code
     *
     * @return Language
     *
     * @throws LanguageNotFoundException
     */
    public function getLanguageByCode(string $code): Language;
}