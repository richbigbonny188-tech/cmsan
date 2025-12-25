<?php
/* --------------------------------------------------------------
 SessionNamePostfixGenerator.php 2021-05-14
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

namespace Gambio\Core\Session;

/**
 * Class SessionNamePostfixGenerator
 *
 * @package Gambio\Core\Session
 */
class SessionNamePostfixGenerator
{
    /**
     * Generates a 16 characters long hash token base on the sha256 algorithm.
     *
     * @return string
     */
    public function generate(): string
    {
        return substr(hash('sha256', mt_rand()), 0, 16);
    }
}