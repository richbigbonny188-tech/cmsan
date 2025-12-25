<?php
/* --------------------------------------------------------------
	ConfigurationStorage.inc.php 2023-03-06
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2023 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

/*
CREATE TABLE IF NOT EXISTS `configuration_storage` (
  `key` varchar(255) NOT NULL,
  `value` varchar(255) DEFAULT NULL,
  `last_modified` timestamp NOT NULL DEFAULT '1000-01-01 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`key`)
) ENGINE=MyISAM
*/

use Gambio\Core\Configuration\Compatibility\ConfigurationStorageRepository;
use Gambio\Core\Configuration\Compatibility\ConfigurationStorageRepositoryBuilder;

/**
 * Class ConfigurationStorage
 */
class ConfigurationStorage
{
    const CS_SEPARATOR = '/';
    
    /**
     * @var string
     */
    protected $_namespace;
    
    /**
     * @var bool
     */
    protected $_use_cache;
    
    /**
     * @var CI_DB_query_builder
     */
    protected $queryBuilder;
    
    /**
     * @var ConfigurationStorageRepository
     */
    protected $repository;
    
    
    /**
     * ConfigurationStorage constructor.
     *
     * @param string $p_namespace
     */
    public function __construct($p_namespace = '')
    {
        $container = LegacyDependencyContainer::getInstance();
        
        /** @var ConfigurationStorageRepositoryBuilder $builder */
        $builder          = $container->get(ConfigurationStorageRepositoryBuilder::class);
        $this->repository = $builder->build($p_namespace);
        
        // left old member for compatibility purposes
        $this->_namespace = $p_namespace;
        $this->_use_cache = false;
        
        $this->queryBuilder = StaticGXCoreLoader::getDatabaseQueryBuilder();
    }
    
    
    /*
    ** Create/Update
    */
    
    public function set($p_key, $p_value)
    {
        $this->repository->set($p_key, $p_value);
        
        return;
    }
    
    
    public function set_all(array $p_tree)
    {
        $this->repository->setAll($p_tree);
    }
    
    
    /*
    ** Read
    */
    
    public function get($p_key)
    {
        // the function returns false instead of null to keep compatibility
        $value = $this->repository->get($p_key);
        
        return $value !== null && $value !== '' ? $value : false;
    }
    
    
    public function is($p_key)
    {
        return $this->repository->is($p_key);
    }
    
    
    public function get_all($p_prefix = '')
    {
        return $this->repository->getAll($p_prefix);
    }
    
    
    public function get_all_tree($p_prefix = '')
    {
        return $this->repository->getAllTree($p_prefix);
    }
    
    
    /*
    ** Delete
    */
    
    public function delete($p_key)
    {
        $this->repository->delete($p_key);
    }
    
    
    /**
     * deletes entire namespace or a subtree from the namespace
     */
    public function delete_all($p_prefix = '')
    {
        $this->repository->deleteAll($p_prefix);
    }
    
    
    /*
    ** Helper functions
    */
    
    protected function _make_db_key($p_key)
    {
        return $this->_namespace . self::CS_SEPARATOR . $p_key;
    }
    
    
    protected function _convert_to_tree_array(array $p_flat_array)
    {
        $t_out_array = [];
        foreach ($p_flat_array as $t_flat_key => $t_value) {
            $t_split_key    = explode(self::CS_SEPARATOR, $t_flat_key);
            $t_current_skey = array_shift($t_split_key);
            $t_current_node =& $t_out_array;
            while (empty($t_split_key) !== true) {
                if (isset($t_current_node[$t_current_skey])) {
                    if (is_array($t_current_node[$t_current_skey]) !== true) {
                        $t_current_node_value            = $t_current_node[$t_current_skey];
                        $t_current_node[$t_current_skey] = ['_' => $t_current_node_value];
                    }
                } else {
                    $t_current_node[$t_current_skey] = [];
                }
                $t_current_node =& $t_current_node[$t_current_skey];
                $t_current_skey = array_shift($t_split_key);
            }
            $t_current_node[$t_current_skey] = $t_value;
        }
        
        return $t_out_array;
    }
    
    
    public function _flatten_array(array $p_tree_array, $p_prefix = '')
    {
        $t_out_array = [];
        if (empty($p_prefix)) {
            $t_top_prefix = '';
        } else {
            $t_top_prefix = $p_prefix . self::CS_SEPARATOR;
        }
        
        foreach ($p_tree_array as $t_key => $t_value) {
            if (is_array($t_value)) {
                $t_flattened_sub_tree = $this->_flatten_array($t_value, $t_key);
                foreach ($t_flattened_sub_tree as $subtree_key => $subtree_value) {
                    $t_out_array[$t_top_prefix . $subtree_key] = $subtree_value;
                }
            } else {
                if ($t_key === '_') {
                    $t_out_array[$p_prefix] = $t_value;
                } else {
                    $t_out_array[$p_prefix . self::CS_SEPARATOR . $t_key] = $t_value;
                }
            }
        }
        
        return $t_out_array;
    }
}

