<?php
/*--------------------------------------------------------------
   AdditionalFieldsRepository.php 2021-07-02
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\MainComponents\Services\Core\AdditionalFields\Services;

use Gambio\MainComponents\Services\Core\AdditionalFields\Model\AdditionalField;
use Gambio\MainComponents\Services\Core\AdditionalFields\Model\Collections\AdditionalFields;
use Gambio\MainComponents\Services\Core\AdditionalFields\Model\Collections\FieldIds;
use Gambio\MainComponents\Services\Core\AdditionalFields\Model\ValueObjects\FieldId;
use Gambio\MainComponents\Services\Core\AdditionalFields\Services\Exceptions\AdditionalFieldDoesNotExistException;
use Gambio\MainComponents\Services\Core\AdditionalFields\Services\Exceptions\CreationOfAdditionalFieldFailedException;
use Gambio\MainComponents\Services\Core\AdditionalFields\Services\Exceptions\DeletionOfAdditionalFieldsFailedException;
use Gambio\MainComponents\Services\Core\AdditionalFields\Services\Exceptions\StorageOfAdditionalFieldsFailedException;

/**
 * Interface AdditionalFieldsRepository
 * @package Gambio\MainComponents\Services\Core\AdditionalFields\Services
 */
interface AdditionalFieldsRepository
{
    /**
     * Returns a specific additional field based on the given ID.
     *
     * @param FieldId $fieldId
     *
     * @return AdditionalField
     *
     * @throws AdditionalFieldDoesNotExistException
     */
    public function getAdditionalFieldById(FieldId $fieldId): AdditionalField;
    
    
    /**
     * Returns a collection of all additional fields.
     *
     * @param int|null    $page
     * @param int|null    $perPage
     * @param string|null $searchTerm
     * @param string|null $sortBy
     *
     * @return AdditionalFields
     */
    public function getAllAdditionalFields(
        ?int $page = null,
        ?int $perPage = null,
        ?string $searchTerm = null,
        ?string $sortBy = null
    ): AdditionalFields;
    
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
     * @param FieldIds $additionalFieldIds
     *
     * @throws DeletionOfAdditionalFieldsFailedException
     */
    public function deleteAdditionalFields(FieldIds $additionalFieldIds): void;
}