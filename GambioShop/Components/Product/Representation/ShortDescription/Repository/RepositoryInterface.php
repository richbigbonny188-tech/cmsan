<?php
/**
 * RepositoryInterface.php 2020-3-18
 * Gambio GmbH
 * http://www.gambio.de
 * Copyright (c) 2020 Gambio GmbH
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
 */

declare(strict_types=1);

namespace Gambio\Shop\Product\Representation\ShortDescription\Repository;

use Gambio\Shop\Language\ValueObjects\LanguageId;
use Gambio\Shop\Product\Representation\ShortDescription\Exceptions\ShortDescriptionNotFoundException;
use Gambio\Shop\SellingUnit\Presentation\ValueObjects\ShortDescription;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\SellingUnitId;

/**
 * Interface RepositoryInterface
 * @package Gambio\Shop\Product\Representation\ShortDescription\Repository
 */
interface RepositoryInterface
{
    /**
     * @param SellingUnitId $sellingUnitId
     *
     * @return ShortDescription
     * @throws ShortDescriptionNotFoundException
     */
    public function shortDescription(SellingUnitId $sellingUnitId): ShortDescription;
}