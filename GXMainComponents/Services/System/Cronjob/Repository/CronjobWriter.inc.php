<?php
/* --------------------------------------------------------------
   CronjobWriter.inc.php 2023-03-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

use Gambio\Core\Configuration\Compatibility\ConfigurationStorageRepositoryBuilder;

/**
 * Class CronjobWriter
 */
class CronjobWriter implements CronjobWriterInterface
{
    /**
     * @var \CI_DB_query_builder
     */
    protected $db;
    
    /**
     * @var ConfigurationStorageRepositoryBuilder
     */
    protected $storageBuilder;
    
    
    /**
     * CronjobWriter constructor.
     *
     * @param \CI_DB_query_builder                  $db
     * @param ConfigurationStorageRepositoryBuilder $storageBuilder
     */
    public function __construct(CI_DB_query_builder $db, ConfigurationStorageRepositoryBuilder $storageBuilder)
    {
        $this->db             = $db;
        $this->storageBuilder = $storageBuilder;
    }
    
    
    /**
     * Saves cronjob configuration into the storage.
     *
     * @param \StringType         $cronjob
     * @param \KeyValueCollection $data
     *
     * @return $this|\CronjobWriterInterface Same instance for chained method calls.
     */
    public function save(StringType $cronjob, KeyValueCollection $data)
    {
        $namespace = 'cronjobs/' . $cronjob->asString();
        $storage   = $this->storageBuilder->build($namespace);
        
        foreach ($data as $key => $value) {
            if ($key !== 'page_token') {
                $storage->set($key, $value);
            }
        }
        
        return $this;
    }
}