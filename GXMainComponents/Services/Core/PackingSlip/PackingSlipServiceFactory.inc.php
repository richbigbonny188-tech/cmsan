<?php
/* --------------------------------------------------------------
   PackingSlipServiceFactory.inc.php 2018-05-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class PackingSlipServiceFactory
 */
class PackingSlipServiceFactory
{
    /**
     * @var CI_DB_query_builder
     */
    protected $db;
    
    
    /**
     * PackingSlipServiceFactory constructor.
     *
     * @param \CI_DB_query_builder $queryBuilder
     */
    public function __construct(CI_DB_query_builder $queryBuilder)
    {
        $this->db = $queryBuilder;
    }
    
    
    /**
     * @return bool|\PackingSlipService
     */
    public function createPackingSlipService()
    {
        return MainFactory::create('PackingSlipService',
                                   $this->createPackingSlipRepository(),
                                   $this->createFileStorage());
    }
    
    
    /**
     * @return bool|\PackingSlipRepository
     */
    protected function createPackingSlipRepository()
    {
        return MainFactory::create('PackingSlipRepository',
                                   $this->createPackingSlipReader(),
                                   $this->createPackingSlipDeleter());
    }
    
    
    /**
     * @return bool|\PackingSlipReader
     */
    protected function createPackingSlipReader()
    {
        return MainFactory::create('PackingSlipReader', $this->db, $this->createPackingSlipFactory());
    }
    
    
    /**
     * @return bool|\PackingSlipDeleter
     */
    protected function createPackingSlipDeleter()
    {
        return MainFactory::create('PackingSlipDeleter', $this->db);
    }
    
    
    /**
     * @return bool|\DocumentFileStorage
     */
    protected function createFileStorage()
    {
        return MainFactory::create('DocumentFileStorage', $this->createPackingSlipDirectory());
    }
    
    
    /**
     * @return \WritableDirectory
     *
     * @throws \InvalidArgumentException
     */
    protected function createPackingSlipDirectory()
    {
        return new WritableDirectory($this->createSettings()->getPackingSlipDirPath());
    }
    
    
    /**
     * @return \EnvPackingSlipServiceSettings
     */
    protected function createSettings()
    {
        return new EnvPackingSlipServiceSettings();
    }
    
    
    protected function createPackingSlipFactory()
    {
        return MainFactory::create('PackingSlipFactory', $this->db);
    }
}