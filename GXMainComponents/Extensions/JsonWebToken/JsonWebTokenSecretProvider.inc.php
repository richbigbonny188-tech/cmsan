<?php
/* --------------------------------------------------------------
   JsonWebTokenSecretProvider.inc.php 2018-07-26
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class JsonWebTokenSecretProvider
 *
 * Creates random secret for JWT authentication.
 */
class JsonWebTokenSecretProvider
{
    const SECRET_LENGTH     = 64;
    const SECRET_CONFIG_KEY = 'REST_API_SECRET';
    
    
    /**
     * Returns a secret for JWT authentication
     *
     * Secret is stored in gm_configuration with key REST_API_SECRET. If none is found a new secret will be created
     * using random_bytes(), openssl_random_pseudo_bytes(), or mt_rand().
     *
     * @return string
     */
    public static function getSecret()
    {
        $apiSecret = (string)gm_get_conf(self::SECRET_CONFIG_KEY, 'ASSOC', true);
        if (empty($apiSecret)) {
            if (function_exists('random_bytes')) {
                $apiSecret = random_bytes(self::SECRET_LENGTH);
            } elseif (function_exists('openssl_random_pseudo_bytes')) {
                $strong    = false;
                $apiSecret = openssl_random_pseudo_bytes(self::SECRET_LENGTH, $strong);
                $apiSecret = $strong === true && $apiSecret !== false ? $apiSecret : '';
            }
            if (empty($apiSecret)) {
                $apiSecret = '';
                for ($i = 0; $i < self::SECRET_LENGTH; $i++) {
                    $apiSecret[$i] = chr(mt_rand(0, 254));
                }
            }
            $apiSecret = bin2hex($apiSecret);
            gm_set_conf(self::SECRET_CONFIG_KEY, $apiSecret);
        }
        
        return $apiSecret;
    }
    
    
    /**
     * Resets REST API secret.
     *
     * ATTENTION: Calling this method will irrevocably invalidate each and every JWT token currently in use!
     * (Unless a database backup with the old secret is available.)
     *
     */
    public static function resetSecret()
    {
        gm_set_conf('REST_API_SECRET', '');
    }
}
