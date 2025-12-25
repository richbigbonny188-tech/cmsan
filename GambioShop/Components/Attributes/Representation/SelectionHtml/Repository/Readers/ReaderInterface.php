<?php
/**
 * ReaderInterface.php 2020-3-17
 * Gambio GmbH
 * http://www.gambio.de
 * Copyright (c) 2020 Gambio GmbH
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
 */

declare(strict_types=1);

namespace Gambio\Shop\Attributes\Representation\SelectionHtml\Repository\Readers;

use Gambio\Shop\Attributes\Representation\SelectionHtml\Exceptions\InvalidValueIdsSpecifiedException;
use Gambio\Shop\Attributes\Representation\SelectionHtml\Repository\DTO\AttributeNameAndValueDTO;
use Gambio\Shop\Language\ValueObjects\LanguageId;
use Gambio\Shop\ProductModifiers\Modifiers\ValueObjects\ModifierIdentifierInterface;

/**
 * Interface ReaderInterface
 * @package Gambio\Shop\Attributes\Representation\SelectionHtml\Repository\Readers
 */
interface ReaderInterface
{
    /**
     * @param ModifierIdentifierInterface $identifier
     *
     * @param LanguageId                  $languageId
     *
     * @return AttributeNameAndValueDTO
     * @throws InvalidValueIdsSpecifiedException
     */
    public function selectionData(ModifierIdentifierInterface $identifier, LanguageId $languageId): AttributeNameAndValueDTO;
}