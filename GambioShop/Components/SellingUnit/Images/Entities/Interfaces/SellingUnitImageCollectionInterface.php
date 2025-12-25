<?php
/*--------------------------------------------------------------------
 SellingUnitImageCollectionInterface.php 2022-08-05
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2022 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/
namespace Gambio\Shop\SellingUnit\Images\Entities\Interfaces;

use ArrayAccess;
use Countable;
use Iterator;
use JsonSerializable;

/**
 * Interface SellingUnitImagesCollectionInterface
 * @package Gambio\Shop\SellingUnit\Images\Entities\Interfaces
 */
interface SellingUnitImageCollectionInterface extends Countable, Iterator, ArrayAccess, JsonSerializable
{
    /**
     * @return SellingUnitImageInterface
     */
    #[\ReturnTypeWillChange]
    public function current();
}