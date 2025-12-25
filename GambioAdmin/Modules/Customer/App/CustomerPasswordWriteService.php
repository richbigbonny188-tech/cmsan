<?php
/* --------------------------------------------------------------
   CustomerPasswordWriteService.php 2023-06-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\App;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Gambio\Admin\Modules\Customer\Services\CustomerPasswordWriteService as CustomerPasswordWriteServiceInterface;
use Gambio\Admin\Modules\Customer\Services\Exceptions\InvalidPasswordFormatException;
use Gambio\Core\Configuration\Services\ConfigurationFinder;

/**
 * Class CustomerPasswordWriteService
 *
 * @package Gambio\Admin\Modules\Customer\App
 */
class CustomerPasswordWriteService implements CustomerPasswordWriteServiceInterface
{
    /**
     * @var ConfigurationFinder
     */
    private ConfigurationFinder $configurations;
    
    /**
     * @var Connection
     */
    private Connection $db;
    
    
    /**
     * @param ConfigurationFinder $configurations
     * @param Connection          $db
     */
    public function __construct(ConfigurationFinder $configurations, Connection $db)
    {
        $this->configurations = $configurations;
        $this->db             = $db;
    }
    
    
    /**
     * @inheritDoc
     * @throws Exception
     */
    public function setCustomerPassword(int $customerId, string $password): void
    {
        $minLength = (int)$this->configurations->get('configuration/ENTRY_PASSWORD_MIN_LENGTH', '0');
        if (strlen($password) < $minLength) {
            throw InvalidPasswordFormatException::becauseOfMinLength($minLength);
        }
        
        $this->db->createQueryBuilder()
            ->update('customers')
            ->set('customers_password', ':password')
            ->where('customers_id = :customerId')
            ->setParameter('password', $this->hashPassword($password))
            ->setParameter('customerId', $customerId)
            ->executeQuery();
    }
    
    
    /**
     * @param string $password
     *
     * @return string
     */
    private function hashPassword(string $password): string
    {
        $strategy = $this->configurations->get('gm_configuration/GM_PASSWORD_ENCRYPTION_TYPE', 'password_hash');
        
        switch ($strategy) {
            case 'password_hash':
            default:
                return (string)password_hash($password, PASSWORD_DEFAULT);
        }
    }
}