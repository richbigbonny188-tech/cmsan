<?php
/*--------------------------------------------------------------------
 AdditionalOptionDoesNotExistException.php 2023-06-06
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
 * Class AdditionalOptionDoesNotExistException
 *
 * @package Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services\Exceptions
 */
class AdditionalOptionDoesNotExistException extends Exception
{
    /**
     * @param int $additionalOptionId
     *
     * @return AdditionalOptionDoesNotExistException
     */
    final public static function forAdditionalOptionId(int $additionalOptionId): AdditionalOptionDoesNotExistException
    {
        return new self(sprintf('Additional option with ID %s does not exist.', $additionalOptionId));
    }
}