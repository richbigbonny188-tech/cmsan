<?php
/*--------------------------------------------------------------------
 DeletionOfProductDownloadsFailedException.php 2023-06-21
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
 * Class DeletionOfProductDownloadsFailedException
 *
 * @package Gambio\Admin\Modules\Product\Submodules\Download\Model\Exceptions
 */
class DeletionOfProductDownloadsFailedException extends Exception
{
    /**
     * @param Exception $exception
     *
     * @return DeletionOfProductDownloadsFailedException
     */
    public static function becauseOfException(Exception $exception): DeletionOfProductDownloadsFailedException
    {
        return new static('Could not delete product download because of previous exception.', 0, $exception);
    }
}