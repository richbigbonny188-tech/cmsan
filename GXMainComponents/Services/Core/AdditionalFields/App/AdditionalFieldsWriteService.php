<?php
/*--------------------------------------------------------------
   AdditionalFieldsWriteService.php 2021-08-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\MainComponents\Services\Core\AdditionalFields\App;

use Gambio\MainComponents\Services\Core\AdditionalFields\Model\AdditionalField;
use Gambio\MainComponents\Services\Core\AdditionalFields\Model\Collections\FieldIds;
use Gambio\MainComponents\Services\Core\AdditionalFields\Model\ValueObjects\FieldId;
use Gambio\MainComponents\Services\Core\AdditionalFields\Services\AdditionalFieldFactory;
use Gambio\MainComponents\Services\Core\AdditionalFields\Services\AdditionalFieldsRepository as AdditionalFieldsRepositoryInterface;
use Gambio\MainComponents\Services\Core\AdditionalFields\Services\AdditionalFieldsWriteService as AdditionalFieldsWriteServiceInterface;

/**
 * Class AdditionalFieldsWriteService
 * @package Gambio\MainComponents\Services\Core\AdditionalFields\App
 */
class AdditionalFieldsWriteService implements AdditionalFieldsWriteServiceInterface
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
     * AdditionalFieldsWriteService constructor.
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
    public function createAdditionalField(array $names): FieldIds
    {
        return $this->repository->createAdditionalField($names);
    }
    
    
    /**
     * @inheritDoc
     */
    public function storeAdditionalFields(AdditionalField ...$fields): void
    {
        $this->repository->storeAdditionalFields(...$fields);
    }
    
    
    /**
     * @inheritDoc
     */
    public function deleteAdditionalFields(int ...$additionalFieldIds): void
    {
        $additionalFieldIds = array_map([$this->factory, 'createFieldId'], $additionalFieldIds);
        $additionalFieldIds = $this->factory->createFieldIds(...$additionalFieldIds);
        
        $this->repository->deleteAdditionalFields($additionalFieldIds);
    }
}