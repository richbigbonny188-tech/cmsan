<?php
/* --------------------------------------------------------------
   ConfigurationDbReader.php 2023-06-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Configuration\App\Data\Readers;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Gambio\Admin\Modules\Configuration\App\Exceptions\ConfigurationDoesNotExist;

/**
 * Class ConfigurationDbReader
 *
 * @package Gambio\Admin\Modules\Configuration\App\Data\Readers
 */
class ConfigurationDbReader
{
    /**
     * @var Connection
     */
    private $db;
    
    /**
     * @var array
     */
    private $configurations;
    
    
    /**
     * ConfigurationJsonReader constructor.
     *
     * @param Connection $db
     */
    public function __construct(Connection $db)
    {
        $this->db = $db;
    }
    
    
    /**
     * @return array
     * @throws Exception
     */
    public function getConfigurationsData(): array
    {
        return $this->configurations();
    }
    
    
    /**
     * @param string $key
     *
     * @return array
     *
     * @throws ConfigurationDoesNotExist
     * @throws Exception
     */
    public function getConfigurationDataByKey(string $key): array
    {
        if (array_key_exists($key, $this->configurations()) === false) {
            throw ConfigurationDoesNotExist::withKey($key);
        }
        
        return $this->configurations()[$key];
    }
    
    
    /**
     * @return array
     * @throws Exception
     */
    private function configurations(): array
    {
        if ($this->configurations === null) {
            $this->configurations = [];
            
            // wrapped helper function to create backticks for column definitions
            $bt = function (string $subject) {
                return $this->db->quoteIdentifier($subject);
            };
            
            $configurations = $this->db->createQueryBuilder()
                ->select(implode(', ',
                                 [$bt('key'), $bt('value'), $bt('type')]))
                ->from($bt('gx_configurations'))
                ->where($this->db->createQueryBuilder()->expr()->orX($this->db->createQueryBuilder()
                                                                         ->expr()
                                                                         ->like($bt('key'), ':config_prefix'),
                                                                     $this->db->createQueryBuilder()
                                                                         ->expr()
                                                                         ->like($bt('key'), ':gm_config_prefix')))
                ->setParameter('config_prefix', 'configuration/%')
                ->setParameter('gm_config_prefix',
                               'gm_configuration/%')
                ->executeQuery()
                ->fetchAllAssociative();
            
            $languageConfigurations = $this->languageConfigurations();
            $configurations         = array_merge($configurations, $languageConfigurations);
            
            foreach ($configurations as $configuration) {
                $this->configurations[$configuration['key']] = $configuration;
            }
        }
        
        return $this->configurations;
    }
    
    
    /**
     * @throws Exception
     */
    private function languageConfigurations(): array
    {
        $languageConfigurationQuery = <<<QUERY
SELECT `c`.`key`, `c`.`value`, `l`.`code`
FROM `gx_lang_configurations` as `c`
LEFT JOIN `languages` as `l` ON (`c`.`language_id` = `l`.`languages_id`);
QUERY;
        $configurations             = $this->db->fetchAllAssociative($languageConfigurationQuery);
        $data                       = [];
        foreach ($configurations as $configuration) {
            $key = $configuration['key'];
            if (!array_key_exists($key, $data)) {
                $data[$key] = [];
            }
            $data[$key]['key']                           = $key;
            $data[$key]['type']                          = 'lang';
            $data[$key]['value'][$configuration['code']] = $configuration['value'];
        }
        
        return array_values($data);
    }
}