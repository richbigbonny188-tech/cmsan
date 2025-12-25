<?php

/* --------------------------------------------------------------
   AbstractCreateAccountProcess.inc.php 2019-03-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class AbstractCreateAccountProcess
 *
 * @category   System
 * @package    Extensions
 * @subpackage Customers
 */
abstract class AbstractCreateAccountProcess
{
    /**
     * @var KeyValueCollection $customerCollection
     */
    protected $customerCollection;
    
    /**
     * @var CustomerService $customerWriteService
     */
    protected $customerWriteService;
    
    /**
     * @var CountryService $countryService
     */
    protected $countryService;
    
    /**
     * @var econda $econda
     * @deprecated econda will be removed in the near future
     */
    protected $econda;
    
    
    /**
     * @param CustomerWriteServiceInterface $customerWriteService
     * @param CountryServiceInterface       $countryService
     * @param econda                        $econda
     */
    public function __construct(
        CustomerWriteServiceInterface $customerWriteService,
        CountryServiceInterface $countryService,
        econda $econda = null
    ) {
        $this->customerWriteService = $customerWriteService;
        $this->countryService       = $countryService;
        $this->econda               = $econda;
    }
    
    
    /**
     * @param KeyValueCollection $customerCollection
     * @param GMLogoManager      $logoManager
     *
     * @throws InvalidCustomerDataException
     */
    public function proceedRegistree(KeyValueCollection $customerCollection, GMLogoManager $logoManager)
    {
        $this->customerCollection = $customerCollection;
        
        $isGuest = $this->_validateRegistree();
        $this->_saveRegistree();
        $this->_login();
        if (!$isGuest) {
            $this->_proceedVoucher();
            $this->_proceedTracking();
            $this->_proceedMail($logoManager);
        }
    }
    
    
    /**
     * @param KeyValueCollection $customerCollection
     *
     * @throws InvalidCustomerDataException
     *
     * @deprecated guest accounts can now be created by passing a $customerCollection w/o password to proceedRegistree()
     */
    public function proceedGuest(KeyValueCollection $customerCollection)
    {
        $this->customerCollection = $customerCollection;
        
        $this->_validateGuest();
        $this->_saveGuest();
        $this->_login();
    }
    
    
    /**
     * @throws InvalidCustomerDataException
     */
    abstract protected function _validateRegistree();
    
    
    /**
     * @throws InvalidCustomerDataException
     */
    abstract protected function _validateGuest();
    
    
    abstract protected function _saveRegistree();
    
    
    abstract protected function _saveGuest();
    
    
    abstract protected function _login();
    
    
    abstract protected function _proceedVoucher();
    
    
    abstract protected function _proceedTracking();
    
    
    /**
     * @param GMLogoManager $logoManager
     */
    abstract protected function _proceedMail(GMLogoManager $logoManager);
}