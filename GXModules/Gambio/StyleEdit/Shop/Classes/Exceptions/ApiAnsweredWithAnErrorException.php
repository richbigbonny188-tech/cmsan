<?php
/*--------------------------------------------------------------------
 ApiAnsweredWithAnErrorException.php 2020-3-6
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

/**
 * Class ApiAnsweredWithAnErrorException
 */
class ApiAnsweredWithAnErrorException extends Exception
{
    /**
     * @param string $request
     *
     * @param string $message
     *
     * @return static
     */
    public static function forRequest(string $request, string $message): self
    {
        return new static('Request: ' . $request . ' answered with the message: ' . $message);
    }
}