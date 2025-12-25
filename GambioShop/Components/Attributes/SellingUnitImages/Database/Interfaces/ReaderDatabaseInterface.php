<?php
/*--------------------------------------------------------------------------------------------------
    ReaderDatabaseInterface.php 2020-02-17
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Shop\Attributes\SellingUnitImages\Database\Interfaces;

interface ReaderDatabaseInterface
{
    /**
     * @param int $attributeId
     * @param int $productId
     * @param int $languageId
     *
     * @return array
     */
    public function getAttributeOptionImagesByProductId(int $attributeId, int $productId, int $languageId) : array;
}