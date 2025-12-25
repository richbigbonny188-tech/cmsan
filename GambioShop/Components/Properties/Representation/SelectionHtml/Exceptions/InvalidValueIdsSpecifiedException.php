<?php
/**
 * InvalidValueIdsSpecifiedException.php 2020-3-17
 * Gambio GmbH
 * http://www.gambio.de
 * Copyright (c) 2020 Gambio GmbH
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
 */

declare(strict_types=1);

namespace Gambio\Shop\Properties\Representation\SelectionHtml\Exceptions;

use Exception;
use Gambio\Shop\Language\ValueObjects\LanguageId;

/**
 * Class InvalidValueIdsSpecifiedException
 * @package Gambio\Shop\Properties\Representation\SelectionHtml\Exceptions
 */
class InvalidValueIdsSpecifiedException extends Exception
{
    /**
     * @param int $valueId
     *
     * @return static
     */
    public static function forValueId(int $valueId): self
    {
        return new static('No value found for the id: ' . $valueId);
    }
    
    
    /**
     * @param int        $optionId
     * @param LanguageId $languageId
     *
     * @return static
     */
    public static function noPropertyNameInLanguage(int $optionId, LanguageId $languageId): self
    {
        return new static('PropertyName not available for propertyId: ' . $optionId . ' & languageId: ' . $languageId->value());
    }
    
    
    /**
     * @param int        $optionId
     * @param LanguageId $languageId
     *
     * @return static
     */
    public static function noPropertyValueNameInLanguage(int $optionId, LanguageId $languageId): self
    {
        return new static('PropertyValueName not available for propertyId: ' . $optionId . ' & languageId: ' . $languageId->value());
    }
}