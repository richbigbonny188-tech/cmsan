<?php
/* --------------------------------------------------------------
   CartItemCollection.php 2016-11-15
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
 * Class CartItemCollection
 *
 * @package HubPublic\Collections
 */
class CartItemCollection extends AbstractCollection
{
    /**
     * CartItemCollection only contains CartItem-objects.
     *
     * @return string String of allowed types
     */
    protected function _getValidType(): string
    {
        return '\HubPublic\ValueObjects\CartItem';  // Use string for PHP compatibility.
    }
}
