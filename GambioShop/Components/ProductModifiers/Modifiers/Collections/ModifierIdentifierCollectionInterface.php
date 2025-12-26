<?php
/*--------------------------------------------------------------------------------------------------
    ModifierIdentifierCollectionInterface.php 2022-08-05
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2022 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\ProductModifiers\Modifiers\Collections;

use ArrayAccess;
use Countable;
use Gambio\Shop\ProductModifiers\Modifiers\ValueObjects\ModifierIdentifierInterface;
use Iterator;
use JsonSerializable;

/**
 * Interface ModifierIdentifierCollectionInterface
 * @package Gambio\Shop\ProductModifiers\Modifiers\Collections
 */
interface ModifierIdentifierCollectionInterface extends Countable, Iterator, ArrayAccess, JsonSerializable
{
    /**
     * @return ModifierIdentifierInterface
     */
    #[\ReturnTypeWillChange]
    public function current();
    
    
    /**
     * @param ModifierIdentifierInterface $value
     *
     * @return int
     */
    public function indexOf(ModifierIdentifierInterface $value): int;

    /**
     * @param ModifierIdentifierCollectionInterface $list
     * @return bool
     */
    public function contains(ModifierIdentifierCollectionInterface $list): bool;

}