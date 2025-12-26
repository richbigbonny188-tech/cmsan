<?php
/*--------------------------------------------------------------
   AdditionalProductFieldReadService.php 2021-08-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\MainComponents\Services\Core\AdditionalProductFields\App;

use Gambio\MainComponents\Services\Core\AdditionalProductFields\Model\Collections\AdditionalProductFields;
use Gambio\MainComponents\Services\Core\AdditionalProductFields\Services\AdditionalProductFieldFactory;
use Gambio\MainComponents\Services\Core\AdditionalProductFields\Services\AdditionalProductFieldReadService as AdditionalProductFieldReadServiceInterface;
use Gambio\MainComponents\Services\Core\AdditionalProductFields\Services\AdditionalProductFieldRepository as AdditionalProductFieldRepositoryInterface;

/**
 * Class AdditionalProductFieldReadService
 * @package Gambio\MainComponents\Services\Core\AdditionalProductFields\App
 */
class AdditionalProductFieldReadService implements AdditionalProductFieldReadServiceInterface
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
    public function getAdditionalProductFields(int $productId): AdditionalProductFields
    {
        return $this->repository->getAdditionalProductFields($this->factory->createProductId($productId));
    }
}