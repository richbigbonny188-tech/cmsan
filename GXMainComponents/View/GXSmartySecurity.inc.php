<?php
/* --------------------------------------------------------------
   GXSmartySecurity.inc.php 2024-05-17
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2024 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class GXSmartySecurity
 * Sets the security policy for Smarty
 */
class GXSmartySecurity extends Smarty_Security
{
    /**
     * Sets allowed PHP functions
     *
     * @var string[]
     */
    public $php_functions = [
        'file_exists',
        'defined',
        'empty',
        'count',
        'isset',
        'strtolower',
        'cookie_consent_panel_is_installed',
        'strstr',
        'cookie_purpose_is_active',
        'cookie_purpose_is_enabled',
        'strpos',
        'trim',
        'array_column',
        'in_array',
        'is_array',
        'time',
        'array_key_exists',
    ];
    
    /**
     * Sets allowed PHP modifiers
     * An empty array means all PHP modifiers are allowed
     *
     * @var string[]
     */
    public $php_modifiers = [];
    
    /**
     * Sets secure directories
     *
     * @var string[]
     */
    public $secure_dir = [
        DIR_FS_CATALOG . 'cache',
        DIR_FS_CATALOG . 'GXModules',
        DIR_FS_CATALOG . 'public/theme',
        DIR_FS_CATALOG . 'public/tmp',
        DIR_FS_CATALOG . 'admin',
    ];
    
}