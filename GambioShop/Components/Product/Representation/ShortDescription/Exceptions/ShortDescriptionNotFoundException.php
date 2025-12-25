<?php
/**
 * ShortDescriptionNotFoundException.php 2020-3-18
 * Gambio GmbH
 * http://www.gambio.de
 * Copyright (c) 2020 Gambio GmbH
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
 */

declare(strict_types=1);

namespace Gambio\Shop\Product\Representation\ShortDescription\Exceptions;

use Exception;
use Gambio\Shop\Language\ValueObjects\LanguageId;

/**
 * Class ShortDescriptionNotFoundException
 * @package Gambio\Shop\Product\Representation\ShortDescription\Exceptions
 */
class ShortDescriptionNotFoundException extends Exception
{
    /**
     * @param int        $productId
     * @param LanguageId $languageId
     *
     * @return static
     */
    public static function forProductIdWithTheLanguageId(int $productId, LanguageId $languageId): self
    {
        $message = 'No short description found for the product with the id: ';
        $message .= $productId . ' and language id: ' . $languageId->value();
        
        return new static($message);
    }
}