<?php
/*--------------------------------------------------------------------------------------------------
    AbstractGroupBuilder.php 2020-06-10
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\Shop\ProductModifiers\Groups\Builders;

use Gambio\Shop\ProductModifiers\Groups\GroupInterface;
use Gambio\Shop\ProductModifiers\Groups\ValueObjects\GroupIdentifierInterface;
use Gambio\Shop\ProductModifiers\Groups\ValueObjects\GroupName;
use Gambio\Shop\ProductModifiers\Groups\ValueObjects\GroupStatus;
use Gambio\Shop\ProductModifiers\Modifiers\Collections\ModifiersCollection;
use Gambio\Shop\ProductModifiers\Modifiers\Collections\ModifiersCollectionInterface;
use Gambio\Shop\ProductModifiers\Modifiers\ModifierInterface;
use Gambio\Shop\ProductModifiers\Presentation\Core\PresentationTypeInterface;

/**
 * Class AbstractGroupBuilder
 * @package Gambio\Shop\ProductModifiers\Groups\Builders
 */
abstract class AbstractGroupBuilder implements GroupBuilderInterface
{
    /**
     * @var GroupIdentifierInterface
     */
    protected $id;

    /**
     * @var ModifierInterface[]
     */
    protected $modifiers = null;

    /**
     * @var GroupName
     */
    protected $name;

    /**
     * @var string
     */
    protected $source;

    /**
     * @var PresentationTypeInterface
     */
    protected $type;

    /**
     * @var GroupStatus
     */
    protected $status;

    public function __construct()
    {
        $this->resetFields();
    }

    /**
     * @param GroupIdentifierInterface $id
     *
     * @return AbstractGroupBuilder
     */
    public function withId(GroupIdentifierInterface $id): GroupBuilderInterface
    {
        $this->id = $id;
        
        return $this;
    }
    
    
    /**
     * @inheritDoc
     */
    public function withModifiers(ModifierInterface ...$modifiers): GroupBuilderInterface
    {
        foreach ($modifiers as $modifier) {
            $this->modifiers()->addModifier($modifier);
        }
        
        return $this;
    }
    
    
    /**
     * @return ModifiersCollectionInterface
     */
    protected function modifiers(): ModifiersCollectionInterface
    {
        if ($this->modifiers === null) {
            $this->modifiers = new ModifiersCollection();
        }
    
        return $this->modifiers;
    }
    
    
    /**
     * @inheritDoc
     */
    public function withName(GroupName $name): GroupBuilderInterface
    {
        $this->name = $name;
        
        return $this;
    }
    
    
    /**
     * @inheritDoc
     */
    public function withType(PresentationTypeInterface $presentationType): GroupBuilderInterface
    {
        $this->type = $presentationType;
        
        return $this;
    }
    
    
    /**
     * @inheritDoc
     */
    public function build(): GroupInterface
    {
        $this->validateGroup();
        $result = $this->createInstance();
        $this->resetFields();
        
        return $result;
    }
    
    
    /**
     * @throws InvalidGroupSourceException
     */
    protected function validateGroup(): void
    {
        if ($this->source === null) {
            throw new InvalidGroupSourceException("Source can't be empty");
        }
    }
    
    
    /**
     * @inheritDoc
     */
    abstract protected function createInstance(): GroupInterface;
    
    
    /**
     * clear all the properties
     */
    protected function resetFields(): void
    {
        $this->id        = null;
        $this->name      = null;
        $this->source    = null;
        $this->modifiers = null;
        $this->status    = new GroupStatus(true);
    }
    
    
    /**
     * @param string $source
     *
     * @return GroupBuilderInterface
     */
    public function withSource(string $source): GroupBuilderInterface
    {
        $this->source = $source;
        
        return $this;
    }

    /**
     * @param GroupStatus $status
     *
     * @return $this|GroupBuilderInterface
     */
    public function withStatus(GroupStatus $status): GroupBuilderInterface
    {
        $this->status = $status;

        return $this;
    }
}