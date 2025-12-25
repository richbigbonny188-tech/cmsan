<?php
/*--------------------------------------------------------------
   WebRequestUserIdentifier.php 2021-12-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

namespace Gambio\Api\Application\Auth\Interfaces;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Interface WebRequestUserIdentifier
 *
 * @package Gambio\Api\Application\Auth\Interfaces
 */
interface WebRequestUserIdentificationService
{
    /**
     * Identifies the user from the provided server request.
     *
     * @param ServerRequestInterface $request
     *
     * @return int Positive integer, if authentication succeeded, otherwise null.
     */
    public function identifyUser(ServerRequestInterface $request): ?int;
}