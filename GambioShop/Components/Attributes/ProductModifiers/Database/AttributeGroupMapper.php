<?php
/*--------------------------------------------------------------------------------------------------
    AttributeGroupMapper.php 2020-02-17
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */
declare(strict_types=1);
namespace Gambio\Shop\Attributes\ProductModifiers\Database;

use Gambio\Shop\ProductModifiers\Database\Core\DTO\Groups\GroupDTO;
use Gambio\Shop\ProductModifiers\Database\Core\Mappers\AbstractGroupMapper;
use Gambio\Shop\Attributes\ProductModifiers\Database\Builders\AttributeGroupBuilder;
use Gambio\Shop\Attributes\ProductModifiers\Database\Builders\AttributeModifierBuilder;
use Gambio\Shop\ProductModifiers\Database\Presentation\Mappers\Interfaces\PresentationMapperInterface;

class AttributeGroupMapper extends AbstractGroupMapper
{
    
    /**
     * AbstractGroupMapper constructor.
     *
     * @param AttributeGroupBuilder       $groupBuilder
     * @param AttributeModifierBuilder    $modifierBuilder
     * @param PresentationMapperInterface $presentationMapper
     */
    public function __construct(
        AttributeGroupBuilder $groupBuilder,
        AttributeModifierBuilder $modifierBuilder,
        PresentationMapperInterface $presentationMapper
    ) {
        parent::__construct($groupBuilder, $modifierBuilder, $presentationMapper);
    }
    
    
    /**
     * @inheritDoc
     */
    protected function canHandle(GroupDTO $dto): bool
    {
        return $dto->source() === AttributeGroup::source();
    }
}