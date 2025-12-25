<?php
/* --------------------------------------------------------------
   UserConfigurationWriter.php 2021-05-21
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\UserConfiguration\App\Data;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Gambio\Core\UserConfiguration\Model\UserConfiguration;
use function Gambio\Admin\Modules\UserConfiguration\App\Data\array_merge;
use function Gambio\Core\Logging\logger;

/**
 * Class UserConfigurationWriter
 *
 * @package Gambio\Core\UserConfiguration\App\Data
 */
class UserConfigurationWriter
{
    /**
     * @var Connection
     */
    private $db;
    
    
    /**
     * UserConfigurationWriter constructor.
     *
     * @param Connection $db
     */
    public function __construct(Connection $db)
    {
        $this->db = $db;
    }
    
    
    /**
     * @param UserConfiguration $configuration
     */
    public function store(UserConfiguration $configuration): void
    {
        $data = [
            'userId' => $configuration->userId(),
            'key'    => $configuration->key(),
            'value'  => $configuration->value(),
        ];
        try {
            $this->db->executeQuery('REPLACE INTO `user_configuration` (`customer_id`, `configuration_key`, `configuration_value`) VALUES  (:userId, :key, :value);',
                                    $data);
        } catch (DBALException $e) {
            logger()->error('Failed to store user configuration',
                            array_merge(['errorMessage' => $e->getMessage()], $data));
        }
    }
}