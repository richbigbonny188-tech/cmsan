<?php
/*--------------------------------------------------------------------------------------------------
    ImageMapper.php 2020-02-06
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\Shop\ProductModifiers\Database\Presentation\Mappers\Implementations;

use Exception;
use Gambio\Shop\ProductModifiers\Database\Core\DTO\Groups\GroupDTO;
use Gambio\Shop\ProductModifiers\Database\Core\DTO\Modifiers\ModifierDTO;
use Gambio\Shop\ProductModifiers\Database\Presentation\Mappers\AbstractPresentationMapper;
use Gambio\Shop\ProductModifiers\Presentation\Core\PresentationInfoInterface;
use Gambio\Shop\ProductModifiers\Presentation\Core\PresentationTypeInterface;
use Gambio\Shop\ProductModifiers\Presentation\Core\ValueObjects\PresentationLabel;
use Gambio\Shop\ProductModifiers\Presentation\Implementations\Image\Builders\ImageInfoBuilder;
use Gambio\Shop\ProductModifiers\Presentation\Implementations\Image\ImageType;
use Gambio\Shop\ProductModifiers\Presentation\Implementations\Image\ValueObjects\ImagePath;

/**
 * Class DropDownSelectInfoMapper
 * @package Gambio\Shop\ProductModifiers\Database\Presentation\Mappers
 */
class ImageMapper extends AbstractPresentationMapper
{
    /**
     * @var ImageMapperSettings
     */
    private $settings;
    
    
    /**
     * DropDownSelectInfoMapper constructor.
     *
     * @param ImageInfoBuilder    $builder
     * @param ImageMapperSettings $settings
     */
    public function __construct(ImageInfoBuilder $builder, ImageMapperSettings $settings)
    {
        parent::__construct($builder);
        $this->settings = $settings;
    }
    
    
    /**
     * @inheritDoc
     */
    protected function canHandlePresentationInfo(ModifierDTO $dto): bool
    {
        return $dto->type() === ImageType::type();
    }
    
    
    /**
     * @inheritDoc
     */
    protected function handlePresentationInfo(ModifierDTO $dto): PresentationInfoInterface
    {

        $webPath  = $this->settings->imagesUrl() . $dto->image();
        $realPath = $this->settings->imagesPath() . $dto->image();
        $this->builder->withPath(new ImagePath($webPath, $realPath));


        $this->builder->withLabel(new PresentationLabel($dto->name()));
        
        return $this->builder->build();
    }
    
    
    /**
     * @inheritDoc
     */
    protected function canHandlePresentationType(GroupDTO $dto): bool
    {
        return $dto->type() === ImageType::type();
    }
    
    
    /**
     * @inheritDoc
     */
    protected function handlePresentationType(GroupDTO $dto): PresentationTypeInterface
    {
        return ImageType::instance();
    }
    
    
}