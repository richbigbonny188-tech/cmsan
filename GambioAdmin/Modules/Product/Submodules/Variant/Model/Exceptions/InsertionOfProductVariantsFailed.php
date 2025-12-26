<?php
/*--------------------------------------------------------------
   InsertionOfProductVariantFailed.php 2023-06-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\Variant\Model\Exceptions;

use Exception;

/**
 * Class InsertionOfProductVariantFailed
 * @package Gambio\Admin\Modules\Product\Submodules\Variant\Model\Exceptions
 */
class InsertionOfProductVariantsFailed extends Exception
{
    /**
     * @param Exception $exception
     *
     * @return InsertionOfProductVariantsFailed
     */
    public static function becauseOfException(Exception $exception): InsertionOfProductVariantsFailed
    {
        return new self('Could not insert product variants because of previous exception.', 0, $exception);
    }
}