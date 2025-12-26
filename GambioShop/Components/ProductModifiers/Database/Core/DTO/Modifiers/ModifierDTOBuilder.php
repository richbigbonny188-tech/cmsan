<?php
/*--------------------------------------------------------------------------------------------------
    ModifierDTOBuilder.php 2021-03-04
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2021 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\Shop\ProductModifiers\Database\Core\DTO\Modifiers;

use Gambio\Shop\ProductModifiers\Groups\ValueObjects\GroupIdentifierInterface;
use Gambio\Shop\ProductModifiers\Modifiers\ValueObjects\ModifierIdentifierInterface;

/**
 * Class ModifierDTOBuilder
 * @package Gambio\Shop\ProductModifiers\Database\Core\DTO\Modifiers
 */
class ModifierDTOBuilder implements ModifierDTOBuilderInterface
{
    /**
     * @var GroupIdentifierInterface
     */
    protected $groupId;
    /**
     * @var ModifierIdentifierInterface
     */
    protected $id;
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $source;

    /**
     * @var string
     */
    protected $type;
    /**
     * @var string
     */
    protected $image;
    /**
     * @var string
     */
    protected $pricePrefix = '';
    /**
     * @var float
     */
    protected $price = 0;
    /**
     * @var bool
     */
    protected $selected = false;
    /**
     * @var bool
     */
    protected $selectable = true;
    /**
     * @var bool
     */
    protected $showStock = false;


    /**
     * @inheritDoc
     */
    public function build(): ModifierDTO
    {
        $result           = new ModifierDTO(
            $this->id,
            $this->groupId,
            $this->name,
            $this->source,
            $this->type,
            $this->image,
            $this->pricePrefix,
            $this->price,
            $this->selected,
            $this->selectable,
            $this->showStock
        );

        $this->id = null;
        $this->groupId = null;
        $this->name = null;
        $this->source = null;
        $this->type = null;
        $this->image = null;
        $this->pricePrefix = '';
        $this->price = 0;
        $this->selected   = false;
        $this->selectable = true;
        $this->showStock = false;

        return $result;
    }

    /**
     * @param GroupIdentifierInterface $groupId
     *
     * @return $this
     */
    public function withGroupId(GroupIdentifierInterface $groupId): ModifierDTOBuilderInterface
    {
        $this->groupId = $groupId;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function withId(ModifierIdentifierInterface $id): ModifierDTOBuilderInterface
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function withImage(string $image): ModifierDTOBuilderInterface
    {
        $this->image = $image;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function withName(string $name): ModifierDTOBuilderInterface
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function withPrice(float $price): ModifierDTOBuilderInterface
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function withPricePrefix(string $pricePrefix): ModifierDTOBuilderInterface
    {
        $this->pricePrefix = $pricePrefix;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function withSource(string $source): ModifierDTOBuilderInterface
    {
        $this->source = $source;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function withType(string $type): ModifierDTOBuilderInterface
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @param bool $selected
     *
     * @return $this|ModifierDTOBuilderInterface
     */
    public function withSelected(bool $selected): ModifierDTOBuilderInterface
    {
        $this->selected = $selected;

        return $this;
    }

    /**
     * @param bool $selectable
     *
     * @return $this|ModifierDTOBuilderInterface
     */
    public function withSelectable(bool $selectable): ModifierDTOBuilderInterface
    {
        $this->selectable = $selectable;

        return $this;
    }
    
    
    public function withShowStock(bool $showStock): ModifierDTOBuilderInterface
    {
        $this->showStock = $showStock;
        
        return $this;
    }

}
