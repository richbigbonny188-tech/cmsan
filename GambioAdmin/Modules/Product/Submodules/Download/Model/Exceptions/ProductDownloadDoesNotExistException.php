<?php
/*--------------------------------------------------------------------
 ProductDownloadDoesNotExistException.php 2023-06-21
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
 * Class ProductDownloadDoesNotExistException
 *
 * @package Gambio\Admin\Modules\Product\Submodules\Download\Model\Exceptions
 */
class ProductDownloadDoesNotExistException extends Exception
{
    /**
     * @param int $productId
     *
     * @return ProductDownloadDoesNotExistException
     */
    final public static function forProductOptionId(int $productId): ProductDownloadDoesNotExistException
    {
        return new static(sprintf('Product option with ID %s does not exist.', $productId));
    }
}