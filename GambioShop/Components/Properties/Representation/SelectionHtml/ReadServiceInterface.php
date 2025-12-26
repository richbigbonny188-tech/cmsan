<?php
/**
 * ReadServiceInterface.php 2020-3-17
 * Gambio GmbH
 * http://www.gambio.de
 * Copyright (c) 2020 Gambio GmbH
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
 */

declare(strict_types=1);

namespace Gambio\Shop\Properties\Representation\SelectionHtml;

use Gambio\Shop\Language\ValueObjects\LanguageId;
use Gambio\Shop\ProductModifiers\Modifiers\Collections\ModifierIdentifierCollectionInterface;
use Gambio\Shop\Properties\Representation\SelectionHtml\Exceptions\InvalidValueIdsSpecifiedException;
use Gambio\Shop\Properties\Representation\SelectionHtml\Generators\ModifierHtmlGenerator;

/**
 * Interface ReadServiceInterface
 * @package Gambio\Shop\Properties\Representation\SelectionHtml
 */
interface ReadServiceInterface
{
    /**
     * @param ModifierIdentifierCollectionInterface $identifiers
     * @param LanguageId                            $languageId
     *
     * @return ModifierHtmlGenerator
     * @throws InvalidValueIdsSpecifiedException
     */
    public function selectionHtmlGenerator(
        ModifierIdentifierCollectionInterface $identifiers,
        LanguageId $languageId
    ): ModifierHtmlGenerator;
}