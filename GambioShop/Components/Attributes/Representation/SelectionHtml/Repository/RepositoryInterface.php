<?php
/*--------------------------------------------------------------------------------------------------
    RepositoryInterface.php 2020-07-08
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\Attributes\Representation\SelectionHtml\Repository;

use Gambio\Shop\Attributes\Representation\SelectionHtml\Exceptions\InvalidValueIdsSpecifiedException;
use Gambio\Shop\Attributes\Representation\SelectionHtml\Generators\ModifierHtmlGenerator;
use Gambio\Shop\Language\ValueObjects\LanguageId;
use Gambio\Shop\ProductModifiers\Modifiers\Collections\ModifierIdentifierCollectionInterface;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\Interfaces\QuantityInterface;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\SellingUnitStockInterface;

/**
 * Interface RepositoryInterface
 * @package Gambio\Shop\Attributes\Representation\SelectionHtml\Repository
 */
interface RepositoryInterface
{
    /**
     * @param ModifierIdentifierCollectionInterface $identifiers
     * @param LanguageId                            $languageId
     *
     * @param QuantityInterface                      $selectedQuantity
     * @param SellingUnitStockInterface             $sellingUnitStock
     *
     * @return ModifierHtmlGenerator
     * @throws InvalidValueIdsSpecifiedException
     */
    public function selectionHtmlGenerator(
        ModifierIdentifierCollectionInterface $identifiers,
        LanguageId $languageId,
        QuantityInterface $selectedQuantity,
        SellingUnitStockInterface $sellingUnitStock
    ): ModifierHtmlGenerator;
}