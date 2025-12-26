<?php
/*--------------------------------------------------------------------------------------------------
    InvalidThemeJsonException.php 2022-06-21
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2022 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

declare(strict_types=1);


namespace Gambio\StyleEdit\Core\Components\Theme\Exceptions;


use Gambio\StyleEdit\Core\TranslatedException;
use Throwable;

class InvalidThemeJsonException extends TranslatedException
{
    public function __construct(array $params = [], Throwable $previous = null)
    {
        parent::__construct(
            'StyleEdit.exceptions.invalid-json-file',
            $params,
            500,
            $previous,
            102
        );
    }
}