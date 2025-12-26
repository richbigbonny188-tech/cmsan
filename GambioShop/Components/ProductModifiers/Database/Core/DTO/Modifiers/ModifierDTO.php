<?php
/*--------------------------------------------------------------------------------------------------
    ModifierDTO.php 2021-03-04
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
 * Class ModifierDTO
 * @package Gambio\Shop\ProductModifiers\Database\Core\DTO\Modifiers
 */
class ModifierDTO
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
    protected $pricePrefix;
    /**
     * @var float
     */
    protected $price;
    /**
     * @var bool
     */
    protected $selected;
    /**
     * @var bool
     */
    private $selectable;
    /**
     * @var bool
     */
    private $showStock;
    
    
    /**
     * ModifierDTO constructor.
     *
     * @param ModifierIdentifierInterface $id
     * @param GroupIdentifierInterface $groupId
     * @param string $name
     * @param string $source
     * @param string $type
     * @param string $image
     * @param string $pricePrefix
     * @param float $price
     * @param bool $selected
     * @param bool $selectable
     * @param bool $showStock
     */
    public function __construct(
        ModifierIdentifierInterface $id,
        GroupIdentifierInterface $groupId,
        string $name,
        string $source,
        string $type,
        string $image,
        string $pricePrefix = '',
        float $price = 0,
        bool $selected = false,
        bool $selectable = true,
        bool $showStock = false
    ) {
        $this->id = $id;
        $this->groupId = $groupId;
        $this->name = $name;
        $this->source = $source;
        $this->type = $type;
        $this->image = $image;
        $this->pricePrefix = $pricePrefix;
        $this->price = $price;
        $this->selected = $selected;
        $this->selectable = $selectable;
        $this->showStock = $showStock;
    }

    /**
     * @return float
     */
    public function price(): float
    {
        return $this->price;
    }

    /**
     * @return string
     */
    public function pricePrefix(): string
    {
        return $this->pricePrefix;
    }

    /**
     * @return GroupIdentifierInterface
     */
    public function groupId(): GroupIdentifierInterface
    {
        return $this->groupId;
    }

    /**
     * @return ModifierIdentifierInterface
     */
    public function id(): ModifierIdentifierInterface
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function image(): string
    {
        return $this->image;
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function source(): string
    {
        return $this->source;
    }

    /**
     * @return string
     */
    public function type(): string
    {
        return $this->type;
    }

    /**
     * @return bool
     */
    public function isSelected(): bool
    {
        return $this->selected;
    }

    /**
     * @return bool
     */
    public function isSelectable(): bool
    {
        return $this->selectable;
    }

    /**
     * @return bool
     */
    public function showStock(): bool
    {
        return $this->showStock;
    }
}
