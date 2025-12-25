<?php
/*--------------------------------------------------------------------------------------------------
    ProductModifierReadServiceInterface.php 2020-02-06
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\Shop\ProductModifiers\Services;

use Gambio\Shop\Language\ValueObjects\LanguageId;
use Gambio\Shop\ProductModifiers\Database\Presentation\Mappers\Exceptions\PresentationMapperNotFoundException;
use Gambio\Shop\ProductModifiers\Groups\Collections\GroupCollectionInterface;
use Gambio\Shop\Product\ValueObjects\ProductId;
use IdType;

interface ProductModifierReadServiceInterface
{
    /**
     * @param ProductId $id
     *
     * @param LanguageId    $languageId
     *
     * @return GroupCollectionInterface
     * @throws PresentationMapperNotFoundException
     */
    public function getGroupsByProduct(ProductId $id, LanguageId $languageId): GroupCollectionInterface;
    
}