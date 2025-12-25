<?php
/*--------------------------------------------------------------------------------------------------
    GroupDTOBuilder.php 2020-06-10
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\Shop\ProductModifiers\Database\Core\DTO\Groups;

use Gambio\Shop\ProductModifiers\Groups\ValueObjects\GroupIdentifierInterface;

/**
 * Class GroupDTOBuilder
 * @package Gambio\Shop\ProductModifiers\Database\Core\DTO\Groups
 */
class GroupDTOBuilder implements GroupDTOBuilderInterface
{
    /**
     * @var GroupIdentifierInterface
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
     * @var bool
     */
    protected $selectable = true;


    /**
     * @param GroupIdentifierInterface $id
     *
     * @return GroupDTOBuilderInterface
     */
    public function withId(GroupIdentifierInterface $id): GroupDTOBuilderInterface
    {
        $this->id = $id;
        
        return $this;
    }
    
    
    /**
     * @param string $source
     *
     * @return GroupDTOBuilderInterface
     */
    public function withSource(string $source): GroupDTOBuilderInterface
    {
        $this->source = $source;
        
        return $this;
    }
    
    
    /**
     * @param string $name
     *
     * @return GroupDTOBuilderInterface
     */
    public function withName(string $name): GroupDTOBuilderInterface
    {
        $this->name = $name;
        
        return $this;
    }
    
    
    /**
     * @param string $type
     *
     * @return GroupDTOBuilderInterface
     */
    public function withType(string $type): GroupDTOBuilderInterface
    {
        $this->type = $type;
        
        return $this;
    }
    
    
    /**
     * @return GroupDTO
     */
    public function build(): GroupDTO
    {
        $result           = new GroupDTO($this->id, $this->name, $this->type, $this->source, $this->selectable);
        $this->id         = null;
        $this->name       = null;
        $this->type       = null;
        $this->source     = null;
        $this->selectable = true;
        
        return $result;
    }

    /**
     * @param bool $selectable
     *
     * @return $this|GroupDTOBuilderInterface
     */
    public function withSelectable(bool $selectable): GroupDTOBuilderInterface
    {
        $this->selectable = $selectable;
        return $this;
    }
}