<?php
/* --------------------------------------------------------------
   CartTotalItemCollection.php 2017-10-10
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the MIT License
   [https://opensource.org/licenses/MIT]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace HubPublic\Collections;

/**
 * Class CartTotalItemCollection
 *
 * @package HubPublic\Collections
 */
class CartTotalItemCollection extends AbstractCollection
{
    /**
     * CartTotalItemCollection only contains CartTotalItem-objects.
     *
     * @return string String of allowed types
     */
    protected function _getValidType(): string
    {
        return '\HubPublic\ValueObjects\CartTotalItem';  // Use string for PHP compatibility.
    }
}
