<?php
/*--------------------------------------------------------------------
 PresentationIdCollectionInterface.php 2022-08-05
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2022 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\SellingUnit\Presentation\Collections;

use ArrayAccess;
use Countable;
use Gambio\Shop\SellingUnit\Presentation\Entities\AbstractPresentationId;
use Iterator;
use JsonSerializable;

/**
 * Interface PresentationIdCollectionInterface
 * @package Gambio\Shop\SellingUnit\Presentation\Collections
 */
interface PresentationIdCollectionInterface extends Countable, Iterator, ArrayAccess, JsonSerializable
{
    /**
     * @return AbstractPresentationId
     */
    #[\ReturnTypeWillChange]
    public function current();
    
    
    /**
     * @return string
     */
    public function __toString(): string;
}