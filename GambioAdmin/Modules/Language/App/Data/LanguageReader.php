<?php
/* --------------------------------------------------------------
   LanguageReader.php 2023-06-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Language\App\Data;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Gambio\Admin\Modules\Language\Model\Exceptions\LanguageNotFoundException;
use Gambio\Admin\Modules\Language\Model\ValueObjects\LanguageCode;
use Gambio\Admin\Modules\Language\Model\ValueObjects\LanguageId;
use Gambio\Core\Filter\Filters;
use Gambio\Core\Filter\Pagination;
use Gambio\Core\Filter\Sorting;
use Gambio\Core\Filter\SqlFilters;
use Gambio\Core\Filter\SqlPagination;
use Gambio\Core\Filter\SqlSorting;

/**
 * Class LanguageReader
 *
 * @package Gambio\Admin\Modules\Language\App\Data
 */
class LanguageReader
{
    /**
     * @var Connection
     */
    private $db;
    
    
    /**
     * LanguageReader constructor.
     *
     * @param Connection $db
     */
    public function __construct(Connection $db)
    {
        $this->db = $db;
    }
    
    
    /**
     * @param LanguageId $id
     *
     * @return array
     *
     * @throws LanguageNotFoundException
     * @throws Exception
     */
    public function getLanguageDataById(LanguageId $id): array
    {
        $languageData = $this->db->createQueryBuilder()
            ->select('languages_id, code, name, language_charset, directory')
            ->from('languages')
            ->where('languages_id = :id')
            ->setParameter('id', $id->value())
            ->executeQuery()
            ->fetchAssociative();
        
        if ($languageData === false) {
            throw LanguageNotFoundException::forId($id->value());
        }
        
        return $languageData;
    }
    
    
    /**
     * @param LanguageCode $code
     *
     * @return array
     *
     * @throws LanguageNotFoundException
     * @throws Exception
     */
    public function getLanguageDataByCode(LanguageCode $code): array
    {
        $languageData = $this->db->createQueryBuilder()
            ->select('languages_id, code, name, language_charset, directory')
            ->from('languages')
            ->where('code = :code')
            ->setParameter('code', $code->value())
            ->executeQuery()
            ->fetchAssociative();
        
        if ($languageData === false) {
            throw LanguageNotFoundException::forCode($code->value());
        }
        
        return $languageData;
    }
    
    
    /**
     * @param Filters|SqlFilters       $filters
     * @param Sorting|SqlSorting       $sorting
     * @param Pagination|SqlPagination $pagination
     *
     * @return array
     * @throws Exception
     */
    public function getFilteredLanguagesData(Filters $filters, Sorting $sorting, Pagination $pagination): array
    {
        $query = $this->db->createQueryBuilder()
            ->select('languages_id, code, name, language_charset, directory')
            ->from('languages');
        
        $filters->applyToQuery($query);
        $sorting->applyToQuery($query);
        $pagination->applyToQuery($query);
        
        return $query->executeQuery()->fetchAllAssociative();
    }
    
    
    /**
     * @param Filters|SqlFilters $filters
     *
     * @return int
     * @throws Exception
     */
    public function getLanguagesTotalCount(Filters $filters): int
    {
        $query = $this->db->createQueryBuilder()
            ->select('languages_id, code, name, language_charset, directory')
            ->from('languages');
        $filters->applyToQuery($query);
        
        return $query->executeQuery()->rowCount();
    }
    
    
    /**
     * @return array
     * @throws Exception
     */
    public function getAllLanguagesData(): array
    {
        return $this->db->createQueryBuilder()
            ->select('languages_id, code, name, language_charset, directory')
            ->from('languages')
            ->executeQuery()
            ->fetchAllAssociative();
    }
}