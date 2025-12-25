<?php
/*--------------------------------------------------------------
   AdditionalProductFieldsRepository.php 2021-08-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\MainComponents\Services\Core\AdditionalProductFields\App;

use Gambio\MainComponents\Services\Core\AdditionalProductFields\App\Data\AdditionalProductFieldsMapper;
use Gambio\MainComponents\Services\Core\AdditionalProductFields\App\Data\AdditionalProductFieldsReader;
use Gambio\MainComponents\Services\Core\AdditionalProductFields\App\Data\AdditionalProductFieldsWriter;
use Gambio\MainComponents\Services\Core\AdditionalProductFields\Model\AdditionalProductField;
use Gambio\MainComponents\Services\Core\AdditionalProductFields\Model\Collections\AdditionalFieldIds;
use Gambio\MainComponents\Services\Core\AdditionalProductFields\Model\Collections\AdditionalProductFields;
use Gambio\MainComponents\Services\Core\AdditionalProductFields\Model\ValueObjects\AdditionalFieldId;
use Gambio\MainComponents\Services\Core\AdditionalProductFields\Model\ValueObjects\ProductId;
use Gambio\MainComponents\Services\Core\AdditionalProductFields\Services\AdditionalProductFieldRepository as AdditionalProductFieldsRepositoryInterface;

/**
 * Class AdditionalProductFieldsRepository
 * @package Gambio\MainComponents\Services\Core\AdditionalProductFields\App
 */
class AdditionalProductFieldRepository implements AdditionalProductFieldsRepositoryInterface
{
    /**
     * @var AdditionalProductFieldsMapper
     */
    protected $mapper;
    
    /**
     * @var AdditionalProductFieldsReader
     */
    protected $reader;
    
    /**
     * @var AdditionalProductFieldsWriter
     */
    protected $writer;
    
    
    /**
     * AdditionalProductFieldsRepository constructor.
     *
     * @param AdditionalProductFieldsMapper $mapper
     * @param AdditionalProductFieldsReader $reader
     * @param AdditionalProductFieldsWriter $writer
     */
    public function __construct(
        AdditionalProductFieldsMapper $mapper,
        AdditionalProductFieldsReader $reader,
        AdditionalProductFieldsWriter $writer
    ) {
        $this->mapper = $mapper;
        $this->reader = $reader;
        $this->writer = $writer;
    }
    
    
    /**
     * @inheritDoc
     */
    public function getAdditionalProductFields(ProductId $productId): AdditionalProductFields
    {
        return $this->mapper->mapAdditionalFields($productId, $this->reader->getAdditionalProductFields($productId));
    }
    
    
    /**
     * @inheritDoc
     */
    public function createAdditionalProductField(
        ProductId $productId,
        AdditionalFieldId $fieldId,
        array $values
    ): void {
    
        $this->writer->createAdditionalProductField($productId, $fieldId, $values);
    }
    
    
    /**
     * @inheritDoc
     */
    public function storeAdditionalProductField(AdditionalProductField ...$productFields): void
    {
        $this->writer->storeAdditionalProductField(...$productFields);
    }
    
    
    /**
     * @inheritDoc
     */
    public function deleteAdditionalProductField(ProductId $productId, AdditionalFieldIds $additionalFieldIds): void
    {
        $this->writer->deleteAdditionalProductField($productId, $additionalFieldIds);
    }
}