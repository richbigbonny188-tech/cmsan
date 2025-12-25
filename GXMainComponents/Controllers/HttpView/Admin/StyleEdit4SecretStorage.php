<?php
/* --------------------------------------------------------------
  StyleEdit4SecretStorage.php 2020-07-08
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

/**
 * Class StyleEdit4SecretStorage
 */
class StyleEdit4SecretStorage extends ConfigurationStorage
{
    protected const SECRET_STORAGE_NAMESPACE = 'modules/gambio/styleedit/jwt-secret';
    
    
    /**
     * StyleEdit4SecretStorage constructor.
     */
    public function __construct()
    {
        parent::__construct(self::SECRET_STORAGE_NAMESPACE);
    }
    
    
    /**
     * @return string
     */
    public function getSecret(): string
    {
        $result = $this->get('secret');
        
        if ($result === false || $result === '') {
            
            $secret = date('Y/m/d H:i');
            
            $this->set('secret', $secret);
            
            return $secret;
        }
        
        return $result;
    }
}