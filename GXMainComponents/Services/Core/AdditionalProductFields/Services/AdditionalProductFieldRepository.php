<?php
/*--------------------------------------------------------------
   AdditionalProductFieldRepository.php 2021-07-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\MainComponents\Services\Core\AdditionalProductFields\Services;

use Gambio\MainComponents\Services\Core\AdditionalProductFields\Model\AdditionalProductField;
use Gambio\MainComponents\Services\Core\AdditionalProductFields\Model\Collections\AdditionalFieldIds;
use Gambio\MainComponents\Services\Core\AdditionalProductFields\Model\Collections\AdditionalProductFields;
use Gambio\MainComponents\Services\Core\AdditionalProductFields\Model\ValueObjects\AdditionalFieldId;
use Gambio\MainComponents\Services\Core\AdditionalProductFields\Model\ValueObjects\ProductId;
use Gambio\MainComponents\Services\Core\AdditionalProductFields\Services\Exceptions\CreationOfAdditionalProductFieldFailedException;
use Gambio\MainComponents\Services\Core\AdditionalProductFields\Services\Exceptions\DeletionOfAdditionalProductFieldsFailedException;
use Gambio\MainComponents\Services\Core\AdditionalProductFields\Services\Exceptions\StorageOfAdditionalProductFieldsFailedException;

/**
 * Interface AdditionalProductFieldRepository
 * @package Gambio\MainComponents\Services\Core\AdditionalProductFields\Services
 */
interface AdditionalProductFieldRepository
{
    /**
     * @param ProductId $productId
     *
     * @return AdditionalProductFields
     */
    public function getAdditionalProductFields(ProductId $productId): AdditionalProductFields;
    
    
    /**
     * @param ProductId         $productId
     * @param AdditionalFieldId $fieldId
     * @param array             ...$values
     *
     * @throws CreationOfAdditionalProductFieldFailedException
     */
    public function createAdditionalProductField(
        ProductId $productId,
        AdditionalFieldId $fieldId,
        array $values
    ): void;
    
    
    /**
     * @param AdditionalProductField ...$productFields
     *
     * @throws StorageOfAdditionalProductFieldsFailedException
     */
    public function storeAdditionalProductField(AdditionalProductField ...$productFields): void;
    
    
    /**
     * @param ProductId          $productId
     * @param AdditionalFieldIds $additionalFieldIds
     *
     * @throws DeletionOfAdditionalProductFieldsFailedException
     */
    public function deleteAdditionalProductField(ProductId $productId, AdditionalFieldIds $additionalFieldIds): void;
}