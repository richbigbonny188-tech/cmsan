<?php
/**
 * Repository.php 2020-3-18
 * Gambio GmbH
 * http://www.gambio.de
 * Copyright (c) 2020 Gambio GmbH
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
 */

declare(strict_types=1);

namespace Gambio\Shop\Product\Representation\ShortDescription\Repository;

use Gambio\Shop\Product\Representation\ShortDescription\Repository\DTO\ShortDescriptionDto;
use Gambio\Shop\Product\Representation\ShortDescription\Repository\Readers\ReaderInterface;
use Gambio\Shop\SellingUnit\Presentation\ValueObjects\ShortDescription;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\SellingUnitId;

/**
 * Class Repository
 * @package Gambio\Shop\Product\Representation\ShortDescription\Repository
 */
class Repository implements RepositoryInterface
{
    /**
     * @var ReaderInterface
     */
    protected $reader;
    
    
    /**
     * Repository constructor.
     *
     * @param ReaderInterface $reader
     */
    public function __construct(ReaderInterface $reader)
    {
        $this->reader = $reader;
    }
    
    
    /**
     * @inheritDoc
     */
    public function shortDescription(SellingUnitId $sellingUnitId): ShortDescription
    {
        $productId           = $sellingUnitId->productId()->value();
        $languageId          = $sellingUnitId->language();
        $shortDescriptionDto = $this->reader->shortDescription($productId, $languageId);
        
        return $this->createShortDescription($shortDescriptionDto);
    }
    
    
    /**
     * @param ShortDescriptionDto $dto
     *
     * @return ShortDescription
     */
    protected function createShortDescription(ShortDescriptionDto $dto): ShortDescription
    {
        return new ShortDescription($dto->shortDescription());
    }
}