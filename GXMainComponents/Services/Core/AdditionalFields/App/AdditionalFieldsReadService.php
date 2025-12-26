<?php
/*--------------------------------------------------------------
   AdditionalFieldsReadService.php 2021-08-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\MainComponents\Services\Core\AdditionalFields\App;

use Gambio\MainComponents\Services\Core\AdditionalFields\Model\AdditionalField;
use Gambio\MainComponents\Services\Core\AdditionalFields\Model\Collections\AdditionalFields;
use Gambio\MainComponents\Services\Core\AdditionalFields\Services\AdditionalFieldFactory;
use Gambio\MainComponents\Services\Core\AdditionalFields\Services\AdditionalFieldsReadService as AdditionalFieldsReadServiceInterface;
use Gambio\MainComponents\Services\Core\AdditionalFields\Services\AdditionalFieldsRepository as AdditionalFieldsRepositoryInterface;

/**
 * Class AdditionalFieldsReadService
 * @package Gambio\MainComponents\Services\Core\AdditionalFields\App
 */
class AdditionalFieldsReadService implements AdditionalFieldsReadServiceInterface
{
    /**
     * @var AdditionalFieldsRepositoryInterface
     */
    protected $repository;
    
    /**
     * @var AdditionalFieldFactory
     */
    protected $factory;
    
    
    /**
     * AdditionalFieldsReadService constructor.
     *
     * @param AdditionalFieldsRepositoryInterface $repository
     * @param AdditionalFieldFactory              $factory
     */
    public function __construct(
        AdditionalFieldsRepositoryInterface $repository,
        AdditionalFieldFactory $factory
    ) {
        $this->repository = $repository;
        $this->factory    = $factory;
    }
    
    
    /**
     * @inheritDoc
     */
    public function getAdditionalFieldById(int $fieldId): AdditionalField
    {
        $fieldId = $this->factory->createFieldId($fieldId);
        
        return $this->repository->getAdditionalFieldById($fieldId);
    }
    
    
    /**
     * @inheritDoc
     */
    public function getAllAdditionalFields(
        ?int $page = null,
        ?int $perPage = null,
        ?string $searchTerm = null,
        ?string $sortBy = null
    ): AdditionalFields {
        
        return $this->repository->getAllAdditionalFields($page, $perPage, $searchTerm, $sortBy);
    }
}