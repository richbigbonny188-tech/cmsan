<?php
/**
 * ReadService.php 2020-3-18
 * Gambio GmbH
 * http://www.gambio.de
 * Copyright (c) 2020 Gambio GmbH
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
 */

declare(strict_types=1);

namespace Gambio\Shop\Product\Representation\ShortDescription;

use Gambio\Shop\Product\Representation\ShortDescription\Exceptions\ShortDescriptionNotFoundException;
use Gambio\Shop\Product\Representation\ShortDescription\Repository\RepositoryInterface;
use Gambio\Shop\SellingUnit\Presentation\ValueObjects\ShortDescription;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\SellingUnitId;

/**
 * Class ReadService
 * @package Gambio\Shop\Product\Representation\ShortDescription
 */
class ReadService implements ReadServiceInterface
{
    /**
     * @var RepositoryInterface
     */
    protected $repository;
    
    
    /**
     * ReadService constructor.
     *
     * @param RepositoryInterface $repository
     */
    public function __construct(RepositoryInterface $repository)
    {
        $this->repository = $repository;
    }
    
    
    /**
     * @inheritDoc
     */
    public function shortDescription(SellingUnitId $sellingUnitId): ShortDescription
    {
        return $this->repository->shortDescription($sellingUnitId);
    }
}