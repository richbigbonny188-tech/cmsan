<?php
/* --------------------------------------------------------------
   CountryZoneTypeFactory.php 2023-06-09
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

/**
 * Class CountryZoneTypeFactory
 *
 * @package Gambio\Admin\Modules\Configuration\Services\TypeFactories
 */
class CountryZoneTypeFactory implements TypeFactory
{
    /**
     * @var Connection
     */
    private $db;
    
    
    /**
     * CountryZoneTypeFactory constructor.
     *
     * @param Connection $db
     */
    public function __construct(Connection $db)
    {
        $this->db = $db;
    }
    
    
    /**
     * @param array $params
     *
     * @return Type
     * @throws Exception
     */
    public function createType(array $params): Type
    {
        $id              = (isset($params['multiSelect']) && $params['multiSelect']) ? 'multi-select' : 'dropdown';
        $params['items'] = $this->getCountryZones();
        unset($params['multiSelect']);
        
        return Type::create($id, $params);
    }
    
    
    /**
     * @return array
     * @throws Exception
     */
    private function getCountryZones(): array
    {
        return $this->db->createQueryBuilder()
            ->select('`zone_id` as `value`, `zone_name` as `text`')
            ->from('`zones`', '`z`')
            ->leftJoin('`z`', '`gx_configurations`', '`c`', '`z`.`zone_country_id` = `c`.`value`')
            ->where('`c`.`key` = "configuration/STORE_COUNTRY"')
            ->orderBy('zone_name')
            ->executeQuery()
            ->fetchAllAssociative();
    }
}