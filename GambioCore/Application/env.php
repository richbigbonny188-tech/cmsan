<?php
/* --------------------------------------------------------------
   env.php 2023-04-26
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\Application;

/**
 * The env system is still in the experimental phase. Changes to the function up to the complete omission in a future
 * version are possible. Only use the function if you know exactly what you are doing.
 *
 * @param string $key
 * @param null   $default
 *
 * @return mixed|null
 */
function env(string $key, $default = null)
{
    static $env = [];
    
    if (empty($env) && file_exists(__DIR__ . '/../../config/.env.php')) {
        $env = include __DIR__ . '/../../config/.env.php';
        
        if (file_exists(__DIR__ . '/../../config/.env.override.php')) {
            $env = array_merge($env, include __DIR__ . '/../../config/.env.override.php');
        }
    }
    
    return $env[$key] ?? $default;
}