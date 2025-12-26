<?php
/* --------------------------------------------------------------
   OptionReadService.php 2020-04-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Option\App;

use Gambio\Admin\Modules\Option\Model\Collections\Options;
use Gambio\Admin\Modules\Option\Model\Option;
use Gambio\Admin\Modules\Option\Services\OptionFactory;
use Gambio\Admin\Modules\Option\Services\OptionReadService as OptionReadServiceInterface;
use Gambio\Admin\Modules\Option\Services\OptionRepository as OptionRepositoryInterface;

/**
 * Class OptionReadService
 *
 * @package Gambio\Admin\Modules\Option\App
 */
class OptionReadService implements OptionReadServiceInterface
{
    /**
     * @var OptionRepositoryInterface
     */
    private $repository;
    
    /**
     * @var OptionFactory
     */
    private $factory;
    
    
    /**
     * OptionReadService constructor.
     *
     * @param OptionRepositoryInterface $repository
     * @param OptionFactory             $factory
     */
    public function __construct(OptionRepositoryInterface $repository, OptionFactory $factory)
    {
        $this->repository = $repository;
        $this->factory    = $factory;
    }
    
    
    /**
     * @inheritDoc
     */
    public function getOptionById(int $optionId): Option
    {
        return $this->repository->getOptionById($this->factory->createOptionId($optionId));
    }
    
    
    /**
     * @inheritDoc
     */
    public function getAllOptions(): Options
    {
        return $this->repository->getAllOptions();
    }
}