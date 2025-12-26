<?php
/* --------------------------------------------------------------
  GmConfigurationReader.php 2019-08-13
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

/**
 * Class GmConfigurationReader
 */
class GmConfigurationReader implements GmConfigurationReaderInterface
{
    /**
     * @var CI_DB_query_builder
     */
    protected $queryBuilder;
    
    /**
     * @var string
     */
    protected const CONFIGURATION_TABLE = 'gx_configurations';
    
    
    /**
     * GmConfigurationReader constructor.
     *
     * @param CI_DB_query_builder $queryBuilder
     */
    public function __construct(CI_DB_query_builder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }
    
    
    /**
     * @param string $key
     *
     * @return string[]
     * @throws GmConfigurationNotFoundException
     */
    public function getConfigurationByKey(string $key): array
    {
        $prefix = 'gm_configuration/';
        $key = strpos($key, $prefix) !== 0 ? "{$prefix}{$key}" : $key;
        
        $result = $this->queryBuilder->select()
            ->from(self::CONFIGURATION_TABLE)
            ->where('key', $key)
            ->get()
            ->result_array();
        
        if (count($result) === 0) {
            
            throw new GmConfigurationNotFoundException($key);
        }
        
        return array_pop($result);
    }
}