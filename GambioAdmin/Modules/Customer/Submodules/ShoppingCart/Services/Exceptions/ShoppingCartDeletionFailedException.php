<?php
/*--------------------------------------------------------------
   ShoppingCartDeletionFailedException.php 2022-09-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\ShoppingCart\Services\Exceptions;

use Exception;

/**
 * @codeCoverageIgnore
 */
class ShoppingCartDeletionFailedException extends Exception
{
    /**
     * Converts a given Exception into Shopping Cart Deletion Failed Exception
     */
    public static function becauseOfException(Exception $exception): self
    {
        return new self($exception->message);
    }
}