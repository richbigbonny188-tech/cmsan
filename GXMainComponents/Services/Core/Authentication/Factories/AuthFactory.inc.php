<?php
/* --------------------------------------------------------------
   AuthFactory.inc.php 2016-09-08
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class AuthFactory
 *
 * @category   System
 * @package    Authentication
 * @subpackage Factories
 */
class AuthFactory implements AuthFactoryInterface
{
    /**
     * @var PasswordEncryptionSettings $passwordEncryptionSettings
     */
    protected $passwordEncryptionSettings;
    
    
    /**
     * AuthFactory constructor.
     *
     * @param PasswordEncryptionSettings $passwordEncryptionSettings
     */
    public function __construct(PasswordEncryptionSettings $passwordEncryptionSettings)
    {
        $this->passwordEncryptionSettings = $passwordEncryptionSettings;
    }
    
    
    /**
     * Creates a AuthService which is used to provide the authentication methods.
     *
     * @return AuthService
     */
    public function createAuthService()
    {
        $md5Strategy          = MainFactory::create('Md5Strategy');
        $passwordHashStrategy = MainFactory::create('PasswordHashStrategy');
        
        if ($this->passwordEncryptionSettings->getType() === 'md5') {
            if ($this->passwordEncryptionSettings->isRehashingEnabled()) {
                $alternativeStrategies = MainFactory::create('AuthStrategyCollection', [$passwordHashStrategy]);
            } else {
                $alternativeStrategies = MainFactory::create('AuthStrategyCollection', []);
            }
            
            $settings = MainFactory::create('AuthSettings', $md5Strategy, $alternativeStrategies);
        } else {
            if ($this->passwordEncryptionSettings->isRehashingEnabled()) {
                $alternativeStrategies = MainFactory::create('AuthStrategyCollection', [$md5Strategy]);
            } else {
                $alternativeStrategies = MainFactory::create('AuthStrategyCollection', []);
            }
            
            $settings = MainFactory::create('AuthSettings', $passwordHashStrategy, $alternativeStrategies);
        }
        
        /** @var CustomerReadService $customerReadService */
        $customerReadService = StaticGXCoreLoader::getService('CustomerRead');
        
        return MainFactory::create('AuthService', $settings, $customerReadService);
    }
}