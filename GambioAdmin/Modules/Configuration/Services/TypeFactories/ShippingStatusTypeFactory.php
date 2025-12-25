<?php
/* --------------------------------------------------------------
   ShippingStatusTypeFactory.php 2023-06-09
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
use Gambio\Core\Application\ValueObjects\UserPreferences;

/**
 * Class ShippingStatusTypeFactory
 *
 * @package Gambio\Admin\Modules\Configuration\Services\TypeFactories
 */
class ShippingStatusTypeFactory implements TypeFactory
{
    /**
     * @var Connection
     */
    private $db;
    
    /**
     * @var UserPreferences
     */
    private $userPreferences;
    
    
    /**
     * ShippingStatusTypeFactory constructor.
     *
     * @param Connection      $db
     * @param UserPreferences $userPreferences
     */
    public function __construct(Connection $db, UserPreferences $userPreferences)
    {
        $this->db              = $db;
        $this->userPreferences = $userPreferences;
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
        $params['items'] = $this->getShippingStatuses();
        unset($params['multiSelect']);
        
        return Type::create($id, $params);
    }
    
    
    /**
     * @return array
     * @throws Exception
     */
    private function getShippingStatuses(): array
    {
        return $this->db->createQueryBuilder()
            ->select('`shipping_status_id` as `value`, `shipping_status_name` as `text`')
            ->from('`shipping_status`')
            ->where('`language_id` = :languageId')
            ->orderBy('`text`')
            ->setParameter('languageId', $this->userPreferences->languageId())
            ->executeQuery()
            ->fetchAllAssociative();
    }
}