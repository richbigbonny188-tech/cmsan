<?php
/*--------------------------------------------------------------
   GenerationOfProductVariantsFailedException.php 2023-06-27
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
 * Class GenerationOfProductVariantsFailedException
 *
 * @package Gambio\Admin\Modules\Product\Submodules\Variant\Model\Exceptions
 */
class GenerationOfProductVariantsFailedException extends Exception
{
    /**
     * @param int $optionId
     * @param int $optionValueId
     *
     * @return GenerationOfProductVariantsFailedException
     */
    public static function optionValueDoesNotBelongToOption(
        int $optionId,
        int $optionValueId
    ): GenerationOfProductVariantsFailedException {
        $message = 'Option value id "%s" does not belong to option id "%d"';
        $message = sprintf($message, $optionValueId, $optionId);
        
        return new self($message, 1);
    }
    
    
    /**
     * @param int $optionValueId
     *
     * @return GenerationOfProductVariantsFailedException
     */
    public static function optionValueDoesNotExist(int $optionValueId): GenerationOfProductVariantsFailedException
    {
        $message = 'Option value id "%s" does not exist';
        $message = sprintf($message, $optionValueId);
        
        return new self($message, 2);
    }
}