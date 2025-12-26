<?php
/* --------------------------------------------------------------
   LanguageRepository.php 2020-11-23
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
use Gambio\Admin\Modules\Language\Model\ValueObjects\LanguageCode;
use Gambio\Admin\Modules\Language\Model\ValueObjects\LanguageId;
use Gambio\Core\Filter\Filters;
use Gambio\Core\Filter\Pagination;
use Gambio\Core\Filter\Sorting;

/**
 * Interface LanguageRepository
 *
 * @package Gambio\Admin\Modules\Language\Services
 */
interface LanguageRepository
{
    /**
     * Returns a filtered and paginated collection of languages based on the given filter and sorting arguments.
     *
     * @param Filters    $filters
     * @param Sorting    $sorting
     * @param Pagination $pagination
     *
     * @return Languages
     */
    public function filterLanguages(Filters $filters, Sorting $sorting, Pagination $pagination): Languages;
    
    
    /**
     * Returns total count of languages based on the given filter arguments.
     *
     * @param Filters $criteria
     *
     * @return int
     */
    public function getLanguagesTotalCount(Filters $criteria): int;
    
    
    /**
     * Returns all available languages.
     *
     * @return Languages
     */
    public function getAllLanguages(): Languages;
    
    
    /**
     * Returns a specific language by its ID.
     *
     * @param LanguageId $id
     *
     * @return Language
     *
     * @throws LanguageNotFoundException
     */
    public function getLanguageById(LanguageId $id): Language;
    
    
    /**
     * Returns a specific language by its two digit ISO code.
     *
     * @param LanguageCode $code
     *
     * @return Language
     *
     * @throws LanguageNotFoundException
     */
    public function getLanguageByCode(LanguageCode $code): Language;
}