<?php
/*--------------------------------------------------------------
   CountryReader.php 2023-06-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Country\App\Data;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryBuilder;
use Gambio\Admin\Modules\Country\Model\ValueObjects\CountryId;
use Gambio\Admin\Modules\Country\Services\Exceptions\CountryDoesNotExistException;

/**
 * Class CountryReader
 *
 * @package Gambio\Admin\Modules\Country\App\Data
 */
class CountryReader
{
    private Connection $connection;
    
    
    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }
    
    
    /**
     * Returns all available countries.
     *
     * @param int $languageId
     *
     * @return array
     * @throws Exception
     */
    public function getCountries(int $languageId): array
    {
        $result = $this->createCountryQuery($languageId)->executeQuery()->fetchAllAssociative();
        
        return array_map([$this, 'addZonesToCountry'], $result);
    }
    
    
    /**
     * Returns all countries customers that are active.
     *
     * @param int $languageId
     *
     * @return array
     * @throws Exception
     */
    public function getActiveCountries(int $languageId): array
    {
        $result = $this->createCountryQuery($languageId)
            ->where('`c`.`status` = "1"')
            ->executeQuery()
            ->fetchAllAssociative();
        
        return array_map([$this, 'addZonesToCountry'], $result);
    }
    
    
    /**
     * Returns a specific country based on the given ID.
     *
     * @param CountryId $id
     * @param int       $languageId
     *
     * @return array
     * @throws CountryDoesNotExistException
     * @throws Exception
     */
    public function getCountryById(CountryId $id, int $languageId): array
    {
        $result = $this->createCountryQuery($languageId)
            ->where('`c`.`countries_id` = :countries_id')
            ->setParameter('countries_id', $id->value())
            ->executeQuery();
        
        if ($result->rowCount() === 0) {
            throw CountryDoesNotExistException::forCountryId($id);
        }
        
        return $this->addZonesToCountry($result->fetchAssociative());
    }
    
    
    /**
     * @param int $languageId
     *
     * @return QueryBuilder
     */
    private function createCountryQuery(int $languageId): QueryBuilder
    {
        $select = $groupBy = [
            'c.countries_id',
            'c.countries_iso_code_2',
            'c.countries_iso_code_3',
            'af.address_format',
        ];
        
        $select[] = 'lpc.phrase_text AS name';
        $select[] = "IF(`c`.`status` = '1', 'true', 'false') AS is_active";
        
        return $this->connection->createQueryBuilder()
            ->select(implode(',', $select))
            ->from('countries', 'c')
            ->innerJoin('c',
                        'language_phrases_cache',
                        'lpc',
                        '`lpc`.`phrase_name`=`c`.`countries_iso_code_2` AND `lpc`.`language_id`=:language_id AND `lpc`.`section_name`="countries"')
            ->innerJoin('c', 'address_format', 'af', '`c`.`address_format_id`=`af`.`address_format_id`')
            ->groupBy(implode(',', $groupBy))
            ->orderBy('`c`.`countries_id`')
            ->setParameter('language_id', $languageId);
    }
    
    
    /**
     * @param array $country
     *
     * @return array
     */
    private function addZonesToCountry(array $country): array
    {
        $countryId        = (int)$country['countries_id'];
        $country['zones'] = $this->getCountryZones($countryId);
        
        return $country;
    }
    
    
    /**
     * @param int $countryId
     *
     * @return array
     * @throws Exception
     */
    private function getCountryZones(int $countryId): array
    {
        $columns = ['z.zone_id', 'z.zone_code', 'z.zone_name',];
        
        return $this->connection->createQueryBuilder()
            ->select(implode(',', $columns))
            ->from('zones', 'z')
            ->groupBy(implode(',', $columns))
            ->where('z.zone_country_id = :country_id')
            ->setParameter('country_id', $countryId)
            ->executeQuery()
            ->fetchAllAssociative();
    }
}