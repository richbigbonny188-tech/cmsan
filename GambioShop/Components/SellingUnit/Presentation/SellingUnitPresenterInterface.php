<?php
/**
 * SellingUnitPresenterInterface.php 2020-3-18
 * Gambio GmbH
 * http://www.gambio.de
 * Copyright (c) 2020 Gambio GmbH
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
 */

declare(strict_types=1);

namespace Gambio\Shop\SellingUnit\Presentation;

use Gambio\Shop\SellingUnit\Presentation\Collections\PresentationIdCollectionInterface;
use Gambio\Shop\SellingUnit\Presentation\ValueObjects\ProductLink;
use Gambio\Shop\SellingUnit\Presentation\ValueObjects\ShortDescription;

/**
 * Interface SellingUnitPresenterInterface
 * @package Gambio\Shop\SellingUnit\Presentation
 */
interface SellingUnitPresenterInterface
{
    /**
     * @return PresentationIdCollectionInterface
     */
    public function getPresentationIdCollection(): PresentationIdCollectionInterface;
    
    /**
     * @return ProductLink
     */
    public function getProductLink(): ProductLink;
    
    /**
     * @return string
     */
    public function getModifierHtml(): string;
    
    
    /**
     * @return ShortDescription
     */
    public function getShortDescription(): ShortDescription;
}