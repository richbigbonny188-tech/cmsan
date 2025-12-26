<?php
/* --------------------------------------------------------------
   CustomerGroupTypeFactory.php 2023-06-09
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
 * Class CustomerGroupTypeFactory
 *
 * @package Gambio\Admin\Modules\Configuration\Services\TypeFactories
 */
class CustomerGroupTypeFactory implements TypeFactory
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
     * CustomerGroupTypeFactory constructor.
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
        $params['items'] = $this->getCustomerGroups();
        unset($params['multiSelect']);
        
        return Type::create($id, $params);
    }
    
    
    /**
     * @return array
     * @throws Exception
     */
    private function getCustomerGroups(): array
    {
        return $this->db->createQueryBuilder()
            ->select('`customers_status_id` as `value`, `customers_status_name` as `text`')
            ->from('`customers_status`')
            ->where('`language_id` = :languageId')
            ->andWhere('`customers_status_id` NOT IN (0,1)')
            ->orderBy('`customers_status_id`')
            ->setParameter('languageId', $this->userPreferences->languageId())
            ->executeQuery()
            ->fetchAllAssociative();
    }
}