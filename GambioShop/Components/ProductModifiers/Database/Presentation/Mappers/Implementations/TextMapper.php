<?php
/*--------------------------------------------------------------------------------------------------
    TextMapper.php 2020-02-14
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\Shop\ProductModifiers\Database\Presentation\Mappers\Implementations;

use Gambio\Shop\ProductModifiers\Database\Core\DTO\Groups\GroupDTO;
use Gambio\Shop\ProductModifiers\Database\Core\DTO\Modifiers\ModifierDTO;
use Gambio\Shop\ProductModifiers\Database\Presentation\Mappers\AbstractPresentationMapper;
use Gambio\Shop\ProductModifiers\Presentation\Core\PresentationInfoInterface;
use Gambio\Shop\ProductModifiers\Presentation\Core\PresentationTypeInterface;
use Gambio\Shop\ProductModifiers\Presentation\Core\ValueObjects\PresentationLabel;
use Gambio\Shop\ProductModifiers\Presentation\Implementations\Text\Builders\TextInfoBuilder;
use Gambio\Shop\ProductModifiers\Presentation\Implementations\Text\TextType;

/**
 * Class TextMapper
 * @package Gambio\Shop\ProductModifiers\Database\Presentation\Mappers
 */
class TextMapper extends AbstractPresentationMapper
{

    /**
     * TextMapper constructor.
     *
     * @param TextInfoBuilder $builder
     */
    public function __construct(TextInfoBuilder $builder)
    {
        parent::__construct($builder);
    }
    
    
    /**
     * @inheritDoc
     */
    protected function canHandlePresentationInfo(ModifierDTO $dto): bool
    {
        return $dto->type() === TextType::type();
    }
    
    
    /**
     * @inheritDoc
     */
    protected function handlePresentationInfo(ModifierDTO $dto): PresentationInfoInterface
    {
        return $this->builder->withLabel(new PresentationLabel($dto->name()))->build();
    }
    
    
    /**
     * @inheritDoc
     */
    protected function canHandlePresentationType(GroupDTO $dto): bool
    {
        return $dto->type() === TextType::type();
    }
    
    
    /**
     * @inheritDoc
     */
    protected function handlePresentationType(GroupDTO $dto): PresentationTypeInterface
    {
        return TextType::instance();
    }
    
    
}