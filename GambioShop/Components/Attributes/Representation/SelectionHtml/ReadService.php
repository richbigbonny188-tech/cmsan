<?php
/*--------------------------------------------------------------------------------------------------
    ReadService.php 2020-07-08
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\Attributes\Representation\SelectionHtml;

use Gambio\Shop\Attributes\Representation\SelectionHtml\Generators\ModifierHtmlGenerator;
use Gambio\Shop\Attributes\Representation\SelectionHtml\Repository\RepositoryInterface;
use Gambio\Shop\Language\ValueObjects\LanguageId;
use Gambio\Shop\ProductModifiers\Modifiers\Collections\ModifierIdentifierCollectionInterface;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\Interfaces\QuantityInterface;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\SellingUnitStockInterface;

/**
 * Class ReadService
 * @package Gambio\Shop\Attributes\Representation\SelectionHtml
 */
class ReadService implements ReadServiceInterface
{
    /**
     * @var RepositoryInterface
     */
    protected $repository;
    
    
    /**
     * ReadService constructor.
     *
     * @param RepositoryInterface $repository
     */
    public function __construct(RepositoryInterface $repository)
    {
        $this->repository = $repository;
    }
    
    
    /**
     * @inheritDoc
     */
    public function selectionHtmlGenerator(
        ModifierIdentifierCollectionInterface $identifiers,
        LanguageId $languageId,
        QuantityInterface $selectedQuantity,
        SellingUnitStockInterface $sellingUnitStock
    ): ModifierHtmlGenerator {
        
        return $this->repository->selectionHtmlGenerator($identifiers, $languageId, $selectedQuantity, $sellingUnitStock);
    }
}