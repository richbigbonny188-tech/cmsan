<?php
/* --------------------------------------------------------------
   LanguageRepository.php 2020-10-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Language\App\Data;

use Gambio\Admin\Modules\Language\Model\Collections\Languages;
use Gambio\Admin\Modules\Language\Model\Language;
use Gambio\Admin\Modules\Language\Model\ValueObjects\LanguageCode;
use Gambio\Admin\Modules\Language\Model\ValueObjects\LanguageId;
use Gambio\Admin\Modules\Language\Services\LanguageRepository as LanguageRepositoryInterface;
use Gambio\Core\Filter\Filters;
use Gambio\Core\Filter\Pagination;
use Gambio\Core\Filter\Sorting;

/**
 * Class LanguageRepository
 *
 * @package Gambio\Admin\Modules\Language\App\Data
 */
class LanguageRepository implements LanguageRepositoryInterface
{
    /**
     * @var LanguageMapper
     */
    private $mapper;
    
    /**
     * @var LanguageReader
     */
    private $reader;
    
    
    /**
     * LanguageRepository constructor.
     *
     * @param LanguageMapper $mapper
     * @param LanguageReader $reader
     */
    public function __construct(
        LanguageMapper $mapper,
        LanguageReader $reader
    ) {
        $this->mapper = $mapper;
        $this->reader = $reader;
    }
    
    
    /**
     * @inheritDoc
     */
    public function filterLanguages(Filters $filters, Sorting $sorting, Pagination $pagination): Languages
    {
        $parcelServicesData = $this->reader->getFilteredLanguagesData($filters, $sorting, $pagination);
        
        return $this->mapper->mapLanguages($parcelServicesData);
    }
    
    
    /**
     * @inheritDoc
     */
    public function getLanguagesTotalCount(Filters $filters): int
    {
        return $this->reader->getLanguagesTotalCount($filters);
    }
    
    
    /**
     * @inheritDoc
     */
    public function getAllLanguages(): Languages
    {
        $parcelServicesData = $this->reader->getAllLanguagesData();
        
        return $this->mapper->mapLanguages($parcelServicesData);
    }
    
    
    /**
     * @inheritDoc
     */
    public function getLanguageById(LanguageId $id): Language
    {
        $parcelServiceData = $this->reader->getLanguageDataById($id);
        
        return $this->mapper->mapLanguage($parcelServiceData);
    }
    
    
    /**
     * @inheritDoc
     */
    public function getLanguageByCode(LanguageCode $code): Language
    {
        $parcelServiceData = $this->reader->getLanguageDataByCode($code);
        
        return $this->mapper->mapLanguage($parcelServiceData);
    }
}