<?php
/*--------------------------------------------------------------
   AdditionalFieldsReadService.php 2021-07-01
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
use Gambio\MainComponents\Services\Core\AdditionalFields\Services\Exceptions\AdditionalFieldDoesNotExistException;

/**
 * Interface AdditionalFieldsReadService
 * @package Gambio\MainComponents\Services\Core\AdditionalFields\Services
 */
interface AdditionalFieldsReadService
{
    /**
     * Returns a specific additional field based on the given ID.
     *
     * @param int $fieldId
     *
     * @return AdditionalField
     *
     * @throws AdditionalFieldDoesNotExistException
     */
    public function getAdditionalFieldById(int $fieldId): AdditionalField;
    
    
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
}