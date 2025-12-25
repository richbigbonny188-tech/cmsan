<?php
/*--------------------------------------------------------------
   AdditionalFieldsRepository.php 2021-08-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\MainComponents\Services\Core\AdditionalFields\App;

use Gambio\MainComponents\Services\Core\AdditionalFields\App\Data\AdditionalFieldsMapper;
use Gambio\MainComponents\Services\Core\AdditionalFields\App\Data\AdditionalFieldsReader;
use Gambio\MainComponents\Services\Core\AdditionalFields\App\Data\AdditionalFieldsWriter;
use Gambio\MainComponents\Services\Core\AdditionalFields\Model\AdditionalField;
use Gambio\MainComponents\Services\Core\AdditionalFields\Model\Collections\AdditionalFields;
use Gambio\MainComponents\Services\Core\AdditionalFields\Model\Collections\FieldIds;
use Gambio\MainComponents\Services\Core\AdditionalFields\Model\ValueObjects\FieldId;
use Gambio\MainComponents\Services\Core\AdditionalFields\Services\AdditionalFieldsRepository as AdditionalFieldsRepositoryInterface;

/**
 * Class AdditionalFieldsRepository
 * @package Gambio\MainComponents\Services\Core\AdditionalFields\App
 */
class AdditionalFieldsRepository implements AdditionalFieldsRepositoryInterface
{
    /**
     * @var AdditionalFieldsMapper
     */
    protected $mapper;
    
    /**
     * @var AdditionalFieldsReader
     */
    protected $reader;
    
    /**
     * @var AdditionalFieldsWriter
     */
    protected $writer;
    
    
    /**
     * AdditionalFieldsRepository constructor.
     *
     * @param AdditionalFieldsMapper $mapper
     * @param AdditionalFieldsReader $reader
     * @param AdditionalFieldsWriter $writer
     */
    public function __construct(
        AdditionalFieldsMapper $mapper,
        AdditionalFieldsReader $reader,
        AdditionalFieldsWriter $writer
    ) {
        $this->mapper = $mapper;
        $this->reader = $reader;
        $this->writer = $writer;
    }
    
    
    /**
     * @inheritDoc
     */
    public function getAdditionalFieldById(FieldId $fieldId): AdditionalField
    {
        $result = $this->reader->getAdditionalFieldById($fieldId);
        $id     = $fieldId->value();
        
        return $this->mapper->mapAdditionalField($id, $result[$id]);
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
    
        $limit = $offset = null;
        
        if ($page !== null && $perPage !== null) {
    
            $limit  = $perPage;
            $offset = $limit * $page;
        }
    
        $sortBy = $this->mapper->mapSortByFields($sortBy ?? '');
        $data   = $this->reader->getAllAdditionalFields($limit, $offset, $searchTerm, $sortBy);
        
        return $this->mapper->mapAdditionalFields($data);
    }
    
    
    /**
     * @inheritDoc
     */
    public function createAdditionalField(array $names): FieldIds
    {
        $ids = $this->writer->createAdditionalField($names);
        $ids = array_map([$this->mapper, 'createFieldId'], $ids);
        
        return $this->mapper->createFieldIds(...$ids);
    }
    
    
    /**
     * @inheritDoc
     */
    public function storeAdditionalFields(AdditionalField ...$fields): void
    {
        $this->writer->storeAdditionalFields(...$fields);
    }
    
    
    /**
     * @inheritDoc
     */
    public function deleteAdditionalFields(FieldIds $additionalFieldIds): void
    {
        $this->writer->deleteAdditionalFields($additionalFieldIds);
    }
}