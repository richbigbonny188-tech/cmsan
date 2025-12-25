<?php
/*--------------------------------------------------------------------------------------------------
    ModifierIdentifierInterface.php 2020-01-23
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\Shop\ProductModifiers\Modifiers\ValueObjects;

/**
 * Interface ModifierIdentifierInterface
 * @package Gambio\Shop\ProductModifiers\Modifiers\ValueObjects
 */
interface ModifierIdentifierInterface
{
    /**
     * Indicates the implementation type (attributes, properties, etc)
     *
     * @return string
     */
    public function type(): string;
    
    
    /**
     * @return mixed
     */
    public function value();
    
    
    /**
     * @param ModifierIdentifierInterface $id
     *
     * @return bool
     */
    public function equals(ModifierIdentifierInterface $id) : bool;
}