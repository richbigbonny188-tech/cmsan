<?php
/*--------------------------------------------------------------------------------------------------
    AbstractPresentationMapper.php 2020-01-23
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\Shop\ProductModifiers\Database\Presentation\Mappers;

use Gambio\Shop\ProductModifiers\Database\Core\DTO\Groups\GroupDTO;
use Gambio\Shop\ProductModifiers\Database\Core\DTO\Modifiers\ModifierDTO;
use Gambio\Shop\ProductModifiers\Database\Presentation\Mappers\Exceptions\PresentationMapperNotFoundException;
use Gambio\Shop\ProductModifiers\Database\Presentation\Mappers\Interfaces\PresentationMapperInterface;
use Gambio\Shop\ProductModifiers\Presentation\Core\Builder\PresentationInfoBuilderInterface;
use Gambio\Shop\ProductModifiers\Presentation\Core\PresentationInfoInterface;
use Gambio\Shop\ProductModifiers\Presentation\Core\PresentationTypeInterface;

/**
 * Class AbstractPresentationMapper
 * @package Gambio\Shop\ProductModifiers\Database\Presentation\Mappers
 */
abstract class  AbstractPresentationMapper implements PresentationMapperInterface
{
    /**
     * @var PresentationInfoBuilderInterface
     */
    protected $builder;
    /**
     * @var PresentationMapperInterface
     */
    protected $next;
    
    
    /**
     * AbstractPresentationMapper constructor.
     *
     * @param PresentationInfoBuilderInterface $builder
     */
    public function __construct(PresentationInfoBuilderInterface $builder)
    {
        $this->builder = $builder;
    }
    
    
    /**
     * @inheritDoc
     */
    public function createPresentationInfo(ModifierDTO $dto): PresentationInfoInterface
    {
        try {
            if ($this->canHandlePresentationInfo($dto)) {
                return $this->handlePresentationInfo($dto);
            } else {
                return $this->handleNextPresentationInfo($dto);
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }
    
    
    /**
     * @param ModifierDTO $dto
     *
     * @return mixed
     */
    abstract protected function canHandlePresentationInfo(ModifierDTO $dto): bool;
    
    
    /**
     * @param ModifierDTO $dto
     *
     * @return PresentationInfoInterface
     */
    abstract protected function handlePresentationInfo(ModifierDTO $dto): PresentationInfoInterface;
    
    
    /**
     * @param ModifierDTO $dto
     *
     * @return void
     * @throws PresentationMapperNotFoundException
     */
    protected function handleNextPresentationInfo(ModifierDTO $dto): PresentationInfoInterface
    {
        if (isset($this->next)) {
            return $this->next->createPresentationInfo($dto);
        } else {
            throw new PresentationMapperNotFoundException($dto->type());
        }
    }
    
    
    /**
     * @inheritDoc
     *
     */
    public function createPresentationType(GroupDTO $dto): PresentationTypeInterface
    {
        if ($this->canHandlePresentationType($dto)) {
            return $this->handlePresentationType($dto);
        } else {
            return $this->handleNextPresentationType($dto);
        }
    }
    
    
    /**
     * @param GroupDTO $dto
     *
     * @return mixed
     */
    abstract protected function canHandlePresentationType(GroupDTO $dto): bool;
    
    
    /**
     * @param GroupDTO $dto
     *
     * @return PresentationTypeInterface
     */
    abstract protected function handlePresentationType(GroupDTO $dto): PresentationTypeInterface;
    
    
    /**
     * @param GroupDTO $dto
     *
     * @return PresentationTypeInterface
     * @throws Exceptions\PresentationTypeNotFoundException
     */
    protected function handleNextPresentationType(GroupDTO $dto): PresentationTypeInterface
    {
        if (isset($this->next)) {
            return $this->next->createPresentationType($dto);
        } else {
            throw new Exceptions\PresentationTypeNotFoundException($dto->type());
        }
    }
    
    
    /**
     * @param PresentationMapperInterface $next
     */
    public function setNext(PresentationMapperInterface $next): void
    {
        $this->next = $next;
    }
}