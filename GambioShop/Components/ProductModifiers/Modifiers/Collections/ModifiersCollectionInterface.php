<?php
/*--------------------------------------------------------------------------------------------------
    ModifiersCollectionInterface.php 2020-01-23
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\Shop\ProductModifiers\Modifiers\Collections;

use Countable;
use Gambio\Shop\ProductModifiers\Modifiers\ModifierInterface;
use Gambio\Shop\ProductModifiers\Modifiers\ValueObjects\ModifierIdentifierInterface;
use IteratorAggregate;
use JsonSerializable;

/**
 * Interface ModifiersCollectionInterface
 * @package Gambio\Shop\ProductModifiers\Modifiers\Collections
 */
interface ModifiersCollectionInterface extends IteratorAggregate, Countable, JsonSerializable
{
    /**
     * @param ModifierInterface $modifier
     *
     * @return mixed
     */
    public function addModifier(ModifierInterface $modifier);
    
    
    /**
     * @param ModifiersCollectionInterface $modifiers
     *
     * @return mixed
     */
    public function addModifiers(ModifiersCollectionInterface $modifiers);
    
    
    /**
     * @param ModifierInterface $modifier
     *
     * @return mixed
     */
    public function removeModifier(ModifierInterface $modifier);
    
    
    /**
     * @param ModifierIdentifierInterface $id
     *
     * @return ModifierInterface
     * @throws InvalidArgumentException
     */
    public function getById(ModifierIdentifierInterface $id) : ModifierInterface;
    
}