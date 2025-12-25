<?php
/*--------------------------------------------------------------
   AdditionalProductFieldWriteService.php 2021-08-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\MainComponents\Services\Core\AdditionalProductFields\App;

use Gambio\MainComponents\Services\Core\AdditionalProductFields\Model\AdditionalProductField;
use Gambio\MainComponents\Services\Core\AdditionalProductFields\Services\AdditionalProductFieldFactory;
use Gambio\MainComponents\Services\Core\AdditionalProductFields\Services\AdditionalProductFieldRepository as AdditionalProductFieldRepositoryInterface;
use Gambio\MainComponents\Services\Core\AdditionalProductFields\Services\AdditionalProductFieldWriteService as AdditionalProductFieldWriteServiceInterface;

/**
 * Class AdditionalProductFieldWriteService
 * @package Gambio\MainComponents\Services\Core\AdditionalProductFields\App
 */
class AdditionalProductFieldWriteService implements AdditionalProductFieldWriteServiceInterface
{
    /**
     * @var AdditionalProductFieldRepositoryInterface
     */
    protected $repository;
    
    /**
     * @var AdditionalProductFieldFactory
     */
    protected $factory;
    
    
    /**
     * AdditionalProductFieldReadService constructor.
     *
     * @param AdditionalProductFieldRepositoryInterface $repository
     * @param AdditionalProductFieldFactory             $factory
     */
    public function __construct(
        AdditionalProductFieldRepositoryInterface $repository,
        AdditionalProductFieldFactory $factory
    ) {
        $this->repository = $repository;
        $this->factory    = $factory;
    }
    
    /**
     * @inheritDoc
     */
    public function createAdditionalProductFields(int $productId, int $fieldId, array $values): void
    {
        $productId = $this->factory->createProductId($productId);
        $fieldId   = $this->factory->createAdditionalFieldId($fieldId);
        
        $this->repository->createAdditionalProductField($productId, $fieldId, $values);
    }
    
    
    /**
     * @inheritDoc
     */
    public function storeAdditionalProductField(AdditionalProductField ...$productFields): void
    {
        $this->repository->storeAdditionalProductField(...$productFields);
    }


    /**
     * @inheritDoc
     */
    public function deleteAdditionalProductField(int $productId, int ...$additionalFieldIds): void
    {
        $productId          = $this->factory->createProductId($productId);
        $additionalFieldIds = array_map([$this->factory, 'createAdditionalFieldId'], $additionalFieldIds);
        $additionalFieldIds = $this->factory->createAdditionalFieldIds(...$additionalFieldIds);
        
        $this->repository->deleteAdditionalProductField($productId, $additionalFieldIds);
    }
}