<?php
/*--------------------------------------------------------------------
 ReaderInterface.php 2020-3-11
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\Attributes\Representation\Id\Repository\Readers;

use Gambio\Shop\Attributes\Representation\Id\Exceptions\InvalidValueIdsSpecifiedException;
use Gambio\Shop\Attributes\Representation\Id\Repository\DTO\AttributeIdDto;

/**
 * Interface ReaderInterface
 * @package Gambio\Shop\Attributes\Representation\Id\Repository\Readers
 */
interface ReaderInterface
{
    /**
     * @param array $valueIds
     *
     * @return AttributeIdDto[];
     * @throws InvalidValueIdsSpecifiedException
     */
    public function getAttributeIdFromValueId(array $valueIds): array;
}