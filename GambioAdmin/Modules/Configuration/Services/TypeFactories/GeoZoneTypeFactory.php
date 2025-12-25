<?php
/* --------------------------------------------------------------
   GeoZoneTypeFactory.php 2023-06-09
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
 * Class GeoZoneTypeFactory
 *
 * @package Gambio\Admin\Modules\Configuration\Services\TypeFactories
 */
class GeoZoneTypeFactory implements TypeFactory
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
     * GeoZoneTypeFactory constructor.
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
        $none            = $this->textManager->getPhraseText('TEXT_NONE', 'admin_general');
        $id              = (isset($params['multiSelect']) && $params['multiSelect']) ? 'multi-select' : 'dropdown';
        $params['items'] = array_merge(
            [
                [
                    'text'  => $none,
                    'value' => '0',
                ]
            ],
            $this->getGeoZones()
        );
        
        unset($params['multiSelect']);
        
        return Type::create($id, $params);
    }
    
    
    /**
     * @return array
     * @throws Exception
     */
    private function getGeoZones(): array
    {
        return $this->db->createQueryBuilder()->select('`geo_zone_id` as `value`, `geo_zone_name` as `text`')->from(
                '`geo_zones`'
            )->orderBy('`text`')->executeQuery()->fetchAllAssociative();
    }
}