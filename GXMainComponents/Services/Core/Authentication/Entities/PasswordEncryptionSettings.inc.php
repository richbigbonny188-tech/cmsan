<?php
/* --------------------------------------------------------------
   PasswordEncryptionSettingsings.inc.php 2016-08-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class PasswordEncryptionSettings
 *
 * @category   System
 * @package    Authentication
 * @subpackage Entities
 */
class PasswordEncryptionSettings
{
    /**
     * Returns the current password encryption type which is stored in the database.
     *
     * @return string Currently used password encryption type.
     */
    public function getType()
    {
        return (string)gm_get_conf('GM_PASSWORD_ENCRYPTION_TYPE');
    }
    
    
    /**
     * Returns if passwords should be rehashed.
     *
     * @return bool Returns true is rehashing is enabled, false otherwise.
     */
    public function isRehashingEnabled()
    {
        return gm_get_conf('GM_PASSWORD_REENCRYPT') === 'true';
    }
}