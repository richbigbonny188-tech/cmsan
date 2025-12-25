<?php
/*--------------------------------------------------------------
   AdditionalFieldsWriteService.php 2021-07-02
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\MainComponents\Services\Core\AdditionalFields\Services;

use Gambio\MainComponents\Services\Core\AdditionalFields\Model\AdditionalField;
use Gambio\MainComponents\Services\Core\AdditionalFields\Model\Collections\FieldIds;
use Gambio\MainComponents\Services\Core\AdditionalFields\Model\ValueObjects\FieldId;
use Gambio\MainComponents\Services\Core\AdditionalFields\Services\Exceptions\CreationOfAdditionalFieldFailedException;
use Gambio\MainComponents\Services\Core\AdditionalFields\Services\Exceptions\DeletionOfAdditionalFieldsFailedException;
use Gambio\MainComponents\Services\Core\AdditionalFields\Services\Exceptions\StorageOfAdditionalFieldsFailedException;

/**
 * Interface AdditionalFieldsWriteService
 * @package Gambio\MainComponents\Services\Core\AdditionalFields\Services
 */
interface AdditionalFieldsWriteService
{
    /**
     * Creates and stores a new additional field.
     *
     * @param array $names
     *
     * @return FieldIds
     *
     * @throws CreationOfAdditionalFieldFailedException
     */
    public function createAdditionalField(array $names): FieldIds;
    
    
    /**
     * Stores one or more existing additional fields.
     *
     * @param AdditionalField ...$fields
     *
     * @throws StorageOfAdditionalFieldsFailedException
     */
    public function storeAdditionalFields(AdditionalField ...$fields): void;
    
    
    /**
     * Deletes one or more existing additional fields.
     *
     * @param int ...$additionalFieldIds
     *
     * @throws DeletionOfAdditionalFieldsFailedException
     */
    public function deleteAdditionalFields(int ...$additionalFieldIds): void;
}