<?php
/**
 * ReaderInterface.php 2020-3-18
 * Gambio GmbH
 * http://www.gambio.de
 * Copyright (c) 2020 Gambio GmbH
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
 */

declare(strict_types=1);

namespace Gambio\Shop\Product\Representation\ShortDescription\Repository\Readers;

use Gambio\Shop\Language\ValueObjects\LanguageId;
use Gambio\Shop\Product\Representation\ShortDescription\Exceptions\ShortDescriptionNotFoundException;
use Gambio\Shop\Product\Representation\ShortDescription\Repository\DTO\ShortDescriptionDto;

/**
 * Interface ReaderInterface
 * @package Gambio\Shop\Product\Representation\ShortDescription\Repository\Readers
 */
interface ReaderInterface
{
    /**
     * @param int        $productId
     *
     * @param LanguageId $languageId
     *
     * @return ShortDescriptionDto
     * @throws ShortDescriptionNotFoundException
     */
    public function shortDescription(int $productId, LanguageId $languageId): ShortDescriptionDto;
}