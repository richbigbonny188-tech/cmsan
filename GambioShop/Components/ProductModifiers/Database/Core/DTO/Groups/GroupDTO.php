<?php
/*--------------------------------------------------------------------------------------------------
    GroupDTO.php 2020-06-10
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\Shop\ProductModifiers\Database\Core\DTO\Groups;

use Gambio\Shop\ProductModifiers\Database\Core\DTO\Modifiers\ModifierDTO;
use Gambio\Shop\ProductModifiers\Groups\ValueObjects\GroupIdentifierInterface;

/**
 * Class GroupDTO
 * @package Gambio\Shop\ProductModifiers\Database\Core\DTO\Groups
 */
class GroupDTO
{
    /**
     * @var GroupIdentifierInterface
     */
    protected $id;
    /**
     * @var ModifierDTO[]
     */
    protected $modifiers = [];
    /**
     * @var string
     */
    protected $name;
    /**
     * @var string
     */
    protected $type;
    /**
     * @var string
     */
    private $source;
    /**
     * @var bool
     */
    private $selectable;


    /**
     * GroupDTO constructor.
     *
     * @param GroupIdentifierInterface $id
     * @param string $name
     * @param string $type
     * @param string $source
     * @param bool $selectable
     */
    public function __construct(
        GroupIdentifierInterface $id,
        string $name,
        string $type,
        string $source,
        bool $selectable)
    {
        $this->id     = $id;
        $this->name   = $name;
        $this->type   = $type;
        $this->source = $source;
        $this->selectable = $selectable;
    }
    
    
    /**
     * @return GroupIdentifierInterface
     */
    public function id(): GroupIdentifierInterface
    {
        return $this->id;
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
    public function type(): string
    {
        return $this->type;
    }
    
    
    /**
     * @return ModifierDTO[]
     */
    public function modifiers(): array
    {
        return $this->modifiers;
    }
    
    
    /**
     * @param ModifierDTO $modifier
     */
    public function addModifier(ModifierDTO $modifier)
    {
        $this->modifiers[] = $modifier;
    }
    
    
    /**
     * @return string
     */
    public function source(): string
    {
        return $this->source;
    }


    /**
     * @return bool
     */
    public function isSelectable() : bool
    {
        return $this->selectable;
    }
}