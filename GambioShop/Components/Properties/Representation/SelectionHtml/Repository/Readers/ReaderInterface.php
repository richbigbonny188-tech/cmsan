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

namespace Gambio\Shop\Properties\Representation\SelectionHtml\Repository\Readers;

use Gambio\Shop\Language\ValueObjects\LanguageId;
use Gambio\Shop\ProductModifiers\Modifiers\ValueObjects\ModifierIdentifierInterface;
use Gambio\Shop\Properties\Representation\SelectionHtml\Exceptions\InvalidValueIdsSpecifiedException;
use Gambio\Shop\Properties\Representation\SelectionHtml\Repository\DTO\PropertyNameAndValueDto;

/**
 * Interface ReaderInterface
 * @package Gambio\Shop\Properties\Representation\SelectionHtml\Repository\Readers
 */
interface ReaderInterface
{
    /**
     * @param ModifierIdentifierInterface $identifier
     * @param LanguageId                  $languageId
     *
     * @return PropertyNameAndValueDto
     * @throws InvalidValueIdsSpecifiedException
     */
    public function selectionData(ModifierIdentifierInterface $identifier, LanguageId $languageId): PropertyNameAndValueDto;
}