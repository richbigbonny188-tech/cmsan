<?php
/*--------------------------------------------------------------------
 InsertionOfProductDownloadsFailedException.php 2023-06-21
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
 * Class InsertionOfProductDownloadsFailedException
 *
 * @package Gambio\Admin\Modules\Product\Submodules\Download\Model\Exceptions
 */
class InsertionOfProductDownloadsFailedException extends Exception
{
    /**
     * @param Exception $exception
     *
     * @return InsertionOfProductDownloadsFailedException
     */
    public static function becauseOfException(Exception $exception): InsertionOfProductDownloadsFailedException
    {
        return new static('Could not insert product download because of previous exception.', 0, $exception);
    }
}