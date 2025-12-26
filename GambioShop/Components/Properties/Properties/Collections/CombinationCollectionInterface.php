<?php
/*--------------------------------------------------------------------
 CombinationCollectionInterface.php 2022-08-05
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2022 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/
namespace Gambio\Shop\Properties\Properties\Collections;

use Gambio\Shop\Properties\Properties\Entities\Combination;

interface CombinationCollectionInterface   extends \Countable, \Iterator, \ArrayAccess, \JsonSerializable
{
    /**
     * @return Combination
     */
    #[\ReturnTypeWillChange]
    public function current();
}