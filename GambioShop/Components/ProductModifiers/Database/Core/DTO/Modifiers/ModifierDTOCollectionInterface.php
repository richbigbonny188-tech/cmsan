<?php
/*--------------------------------------------------------------------------------------------------
    ModifierDTOCollectionInterface.php 2020-01-23
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\Shop\ProductModifiers\Database\Core\DTO\Modifiers;

use Countable;
use IteratorAggregate;
use JsonSerializable;

/**
 * Interface ModifierDTOCollectionInterface
 * @package Gambio\Shop\ProductModifiers\Database\Core\DTO\Modifiers
 */
interface ModifierDTOCollectionInterface extends IteratorAggregate, Countable, JsonSerializable
{

}