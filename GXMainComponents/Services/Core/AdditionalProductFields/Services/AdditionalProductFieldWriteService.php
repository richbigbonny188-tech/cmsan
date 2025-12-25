<?php
/*--------------------------------------------------------------
   AdditionalProductFieldWriteService.php 2021-07-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\MainComponents\Services\Core\AdditionalProductFields\Services;

use Gambio\MainComponents\Services\Core\AdditionalProductFields\Model\AdditionalProductField;
use Gambio\MainComponents\Services\Core\AdditionalProductFields\Services\Exceptions\CreationOfAdditionalProductFieldFailedException;
use Gambio\MainComponents\Services\Core\AdditionalProductFields\Services\Exceptions\DeletionOfAdditionalProductFieldsFailedException;
use Gambio\MainComponents\Services\Core\AdditionalProductFields\Services\Exceptions\StorageOfAdditionalProductFieldsFailedException;

/**
 * Interface AdditionalProductFieldWriteService
 * @package Gambio\MainComponents\Services\Core\AdditionalProductFields\Services
 */
interface AdditionalProductFieldWriteService
{
    /**
     * @param int   $productId
     * @param int   $fieldId
     * @param array $values
     *
     * @throws CreationOfAdditionalProductFieldFailedException
     */
    public function createAdditionalProductFields(int $productId, int $fieldId, array $values): void;
    
    
    /**
     * @param AdditionalProductField ...$productFields
     *
     * @throws StorageOfAdditionalProductFieldsFailedException
     */
    public function storeAdditionalProductField(AdditionalProductField ...$productFields): void;
    
    
    /**
     * @param int $productId
     * @param int ...$additionalFieldIds
     *
     * @throws DeletionOfAdditionalProductFieldsFailedException
     */
    public function deleteAdditionalProductField(int $productId, int ...$additionalFieldIds): void;
}