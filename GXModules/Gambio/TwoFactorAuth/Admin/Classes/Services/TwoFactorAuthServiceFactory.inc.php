<?php

/* --------------------------------------------------------------
   TwoFactorAuthServiceFactory.inc.php 2018-01-12
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class representing a two-factor-authorization service factory
 */
class TwoFactorAuthServiceFactory
{
    /**
     * Configuration storage
     * @var AuthConfigurationStorage
     */
    protected $configurationStorage;
    
    /**
     * Safety file storage
     * @var AuthSafetyFileStorage
     */
    protected $safetyFileStorage;
    
    /**
     * Tolerated time window drift
     * @var IntType
     */
    protected $toleratedDelay;
    
    /**
     * Read service
     * @var TwoFactorAuthReadService
     */
    protected $readService;
    
    /**
     * Write service
     * @var TwoFactorAuthWriteService
     */
    protected $writeService;
    
    /**
     * Delete service
     * @var TwoFactorAuthDeleteService
     */
    protected $deleteService;
    
    
    /**
     * Create instance
     */
    public function __construct()
    {
        $safetyFileStorageDirectoryPath = new NonEmptyStringType(DIR_FS_CATALOG . 'cache/2fa');
        
        $this->configurationStorage = MainFactory::create('AuthConfigurationStorage');
        $this->safetyFileStorage    = MainFactory::create('AuthSafetyFileStorage', $safetyFileStorageDirectoryPath);
        $this->toleratedDelay       = new IntType(4);
    }
    
    
    /**
     * Return the read service
     * @return TwoFactorAuthReadService Read service
     */
    public function read()
    {
        if ($this->readService !== null) {
            return $this->readService;
        }
        
        $this->readService = MainFactory::create('TwoFactorAuthReadService',
                                                 $this->configurationStorage,
                                                 $this->safetyFileStorage,
                                                 $this->toleratedDelay);
        
        return $this->readService;
    }
    
    
    /**
     * Return the write service
     * @return TwoFactorAuthWriteService Write service
     */
    public function write()
    {
        if ($this->writeService !== null) {
            return $this->writeService;
        }
        
        $this->writeService = MainFactory::create('TwoFactorAuthWriteService',
                                                  $this->configurationStorage,
                                                  $this->safetyFileStorage);
        
        return $this->writeService;
    }
    
    
    /**
     * Return the delete service
     * @return TwoFactorAuthDeleteService Delete service
     */
    public function delete()
    {
        if ($this->deleteService !== null) {
            return $this->deleteService;
        }
        
        $this->deleteService = MainFactory::create('TwoFactorAuthDeleteService',
                                                   $this->configurationStorage,
                                                   $this->safetyFileStorage);
        
        return $this->deleteService;
    }
}