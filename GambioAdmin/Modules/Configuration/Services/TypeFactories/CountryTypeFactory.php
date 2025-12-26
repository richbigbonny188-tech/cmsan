<?php
/* --------------------------------------------------------------
   CountryTypeFactory.php 2023-06-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Configuration\Services\TypeFactories;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Gambio\Admin\Modules\Configuration\Model\Entities\Type;
use Gambio\Core\TextManager\Services\TextManager;

/**
 * Class CountryTypeFactory
 *
 * @package Gambio\Admin\Modules\Configuration\Services\TypeFactories
 */
class CountryTypeFactory implements TypeFactory
{
    /**
     * @var Connection
     */
    private $db;
    
    /**
     * @var TextManager
     */
    private $textManager;
    
    
    /**
     * CountryTypeFactory constructor.
     *
     * @param Connection  $db
     * @param TextManager $textManager
     */
    public function __construct(Connection $db, TextManager $textManager)
    {
        $this->db          = $db;
        $this->textManager = $textManager;
    }
    
    
    /**
     * @param array $params
     *
     * @return Type
     * @throws Exception
     */
    public function createType(array $params): Type
    {
        $params['items'] = $this->getCountries();
        
        return Type::create('dropdown', $params);
    }
    
    
    /**
     * @return array
     * @throws Exception
     */
    private function getCountries(): array
    {
        $countriesData = $this->db->createQueryBuilder()
            ->select('`countries_id` as `value`, `countries_iso_code_2` as `text`')
            ->from('`countries`')
            ->executeQuery()
            ->fetchAllAssociative();
        
        foreach ($countriesData as &$countryData) {
            $countryData['text'] = $this->textManager->getPhraseText($countryData['text'], 'countries');
        }
        unset($countryData);
        
        usort($countriesData,
            static function (array $a, array $b) {
                return strcmp($a['text'], $b['text']);
            });
        
        return $countriesData;
    }
}