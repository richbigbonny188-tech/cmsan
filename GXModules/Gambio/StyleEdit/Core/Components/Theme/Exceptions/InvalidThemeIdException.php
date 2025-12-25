<?php
/*--------------------------------------------------------------------------------------------------
    InvalidThemeIdException.php 2022-06-16
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2022 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\StyleEdit\Core\Components\Theme\Exceptions;


use Gambio\StyleEdit\Core\TranslatedException;
use Throwable;

/**
 * Class InvalidThemeIdException
 * @package Gambio\StyleEdit\Core\Components\Theme\Entities
 */
class InvalidThemeIdException extends TranslatedException
{
    /**
     * InvalidThemeIdException constructor.
     * @param array $params
     * @param Throwable|null $previous
     * @throws \Exception
     */
    public function __construct(array $params = [], Throwable $previous = null)
    {
        parent::__construct(
            'StyleEdit.exceptions.invalid-theme-id',
            $params,
            500,
            $previous,
            101
        );
    }
}