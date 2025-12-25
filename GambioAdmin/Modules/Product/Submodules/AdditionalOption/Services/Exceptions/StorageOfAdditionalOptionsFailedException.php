<?php
/*--------------------------------------------------------------------
 StorageOfProductOptionsFailedException.php 2023-06-06
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2023 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services\Exceptions;

use Exception;

/**
 * Class StorageOfProductOptionsFailedException
 *
 * @package Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services\Exceptions
 */
class StorageOfAdditionalOptionsFailedException extends Exception
{
    /**
     * @param Exception $exception
     *
     * @return StorageOfAdditionalOptionsFailedException
     */
    public static function becauseOfException(Exception $exception): StorageOfAdditionalOptionsFailedException
    {
        return new self('Could not store additional option because of previous exception.', 0, $exception);
    }
}