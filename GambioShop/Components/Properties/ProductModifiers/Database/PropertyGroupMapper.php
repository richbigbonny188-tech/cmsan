<?php
/*--------------------------------------------------------------------------------------------------
    PropertyGroupMapper.php 2020-02-17
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */
declare(strict_types=1);

namespace Gambio\Shop\Properties\ProductModifiers\Database;

use Gambio\Shop\ProductModifiers\Database\Core\DTO\Groups\GroupDTO;
use Gambio\Shop\ProductModifiers\Database\Core\Mappers\AbstractGroupMapper;
use Gambio\Shop\Properties\ProductModifiers\Database\Builders\PropertyGroupBuilder;
use Gambio\Shop\Properties\ProductModifiers\Database\Builders\PropertyModifierBuilder;
use Gambio\Shop\ProductModifiers\Database\Presentation\Mappers\Interfaces\PresentationMapperInterface;

class PropertyGroupMapper extends AbstractGroupMapper
{
    
    /**
     * AbstractGroupMapper constructor.
     *
     * @param PropertyGroupBuilder        $groupBuilder
     * @param PropertyModifierBuilder     $modifierBuilder
     * @param PresentationMapperInterface $presentationMapper
     */
    public function __construct(
        PropertyGroupBuilder $groupBuilder,
        PropertyModifierBuilder $modifierBuilder,
        PresentationMapperInterface $presentationMapper
    ) {
        parent::__construct($groupBuilder, $modifierBuilder, $presentationMapper);
    }
    
    
    /**
     * @inheritDoc
     */
    protected function canHandle(GroupDTO $dto): bool
    {
        return $dto->source() === PropertyGroup::source();
    }
}