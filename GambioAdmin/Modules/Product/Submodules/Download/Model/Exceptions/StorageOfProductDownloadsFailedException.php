<?php
/*--------------------------------------------------------------------
 StorageOfProductDownloadsFailedException.php 2023-06-21
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2023 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\Download\Model\Exceptions;

use Exception;

/**
 * Class StorageOfProductDownloadsFailedException
 * @package Gambio\Admin\Modules\Product\Submodules\Download\Model\Exceptions
 */
class StorageOfProductDownloadsFailedException extends Exception
{
    /**
     * @param Exception $exception
     *
     * @return StorageOfProductDownloadsFailedException
     */
    public static function becauseOfException(Exception $exception): StorageOfProductDownloadsFailedException
    {
        return new static('Could not store product download because of previous exception.', 0, $exception);
    }
}