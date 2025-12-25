<?php
/* --------------------------------------------------------------
   HashStrategy.php 2020-04-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\Auth;

/**
 * Interface HashStrategy
 *
 * @package Gambio\Core\Auth
 */
interface HashStrategy
{
    /**
     * Verifies a provided password by comparing it with the hash.
     *
     * @param string $password
     * @param string $hash
     *
     * @return bool
     */
    public function verify(string $password, string $hash): bool;
    
    
    /**
     * Hashes the provided password.
     *
     * @param string $password
     *
     * @return string
     */
    public function hash(string $password): string;
}