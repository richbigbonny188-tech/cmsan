<?php
/*--------------------------------------------------------------------------------------------------
    AbstractGroupMapper.php 2021-03-04
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2021 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\Shop\ProductModifiers\Database\Core\Mappers;

use Gambio\Shop\ProductModifiers\Database\Core\DTO\Groups\GroupDTO;
use Gambio\Shop\ProductModifiers\Database\Core\Mappers\Interfaces\GroupMapperInterface;
use Gambio\Shop\ProductModifiers\Database\Presentation\Mappers\Exceptions\PresentationMapperNotFoundException;
use Gambio\Shop\ProductModifiers\Database\Presentation\Mappers\Interfaces\PresentationMapperInterface;
use Gambio\Shop\ProductModifiers\Groups\Builders\GroupBuilderInterface;
use Gambio\Shop\ProductModifiers\Groups\Builders\InvalidGroupSourceException;
use Gambio\Shop\ProductModifiers\Groups\GroupInterface;
use Gambio\Shop\ProductModifiers\Groups\ValueObjects\GroupName;
use Gambio\Shop\ProductModifiers\Groups\ValueObjects\GroupStatus;
use Gambio\Shop\ProductModifiers\Modifiers\Builders\ModifierBuilderInterface;
use Gambio\Shop\ProductModifiers\Modifiers\ValueObjects\AdditionalInfo;
use Gambio\Shop\ProductModifiers\Modifiers\ValueObjects\ModifierName;
use Gambio\Shop\ProductModifiers\Modifiers\ValueObjects\ModifierSelectable;
use Gambio\Shop\ProductModifiers\Modifiers\ValueObjects\ModifierSelected;
use InvalidArgumentException;

abstract class AbstractGroupMapper implements GroupMapperInterface
{
    /**
     * @var GroupBuilderInterface
     */
    protected $groupBuilder;
    /**
     * @var GroupMapperInterface
     */
    protected $next;
    /**
     * @var ModifierBuilderInterface
     */
    private $modifierBuilder;
    /**
     * @var PresentationMapperInterface
     */
    private $presentationMapper;
    
    
    /**
     * AbstractGroupMapper constructor.
     *
     * @param GroupBuilderInterface       $groupBuilder
     * @param ModifierBuilderInterface    $modifierBuilder
     * @param PresentationMapperInterface $presentationMapper
     */
    public function __construct(
        GroupBuilderInterface $groupBuilder,
        ModifierBuilderInterface $modifierBuilder,
        PresentationMapperInterface $presentationMapper
    ) {
        $this->groupBuilder       = $groupBuilder;
        $this->presentationMapper = $presentationMapper;
        $this->modifierBuilder    = $modifierBuilder;
    }
    
    
    /**
     * @param GroupMapperInterface $next
     */
    public function setNext(GroupMapperInterface $next): void
    {
        $this->next = $next;
    }
    
    
    /**
     * @inheritDoc
     */
    public function mapGroup(GroupDTO $dto): GroupInterface
    {
        if ($this->canHandle($dto)) {
            return $this->handle($dto);
        } elseif (isset($this->next)) {
            return $this->next->mapGroup($dto);
        } else {
            throw new InvalidArgumentException('The is no mapper to dto ' . $dto->source());
        }
    }
    
    
    /**
     * @param GroupDTO $dto
     *
     * @return bool
     */
    abstract protected function canHandle(GroupDTO $dto): bool;
    
    
    /**
     * @param GroupDTO $dto
     *
     * @return GroupInterface
     * @throws PresentationMapperNotFoundException
     * @throws InvalidGroupSourceException
     */
    protected function handle(GroupDTO $dto): GroupInterface
    {
        $this->groupBuilder->withName(new GroupName($dto->name()))
                           ->withId($dto->id())
                           ->withSource($dto->source())
                           ->withStatus( new GroupStatus($dto->isSelectable()))
                           ->withType($this->presentationMapper->createPresentationType($dto));
        foreach ($dto->modifiers() as $modifierDTO) {
            $value    = $this->presentationMapper->createPresentationInfo($modifierDTO);
            $modifier = $this->modifierBuilder->withId($modifierDTO->id())
                ->withName(new ModifierName($modifierDTO->name()))
                ->withSelected(new ModifierSelected($modifierDTO->isSelected()))
                ->withSelectable(new ModifierSelectable($modifierDTO->isSelectable()))
                ->withAdditionalInfo(new AdditionalInfo($modifierDTO->pricePrefix(),
                                                        $modifierDTO->price(),
                                                        $modifierDTO->showStock()))
                ->withValue($value)
                ->build();
            $this->groupBuilder->withModifiers($modifier);
        }
        
        return $this->groupBuilder->build();
    }
    
    
}
