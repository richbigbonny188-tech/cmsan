<?php
/*--------------------------------------------------------------------------------------------------
    AbstractModifierBuilder.php 2020-06-10
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\Shop\ProductModifiers\Modifiers\Builders;

use Gambio\Shop\ProductModifiers\Modifiers\ModifierInterface;
use Gambio\Shop\ProductModifiers\Modifiers\ValueObjects\ModifierSelectable;
use Gambio\Shop\ProductModifiers\Modifiers\ValueObjects\ModifierIdentifierInterface;
use Gambio\Shop\ProductModifiers\Modifiers\ValueObjects\ModifierName;
use Gambio\Shop\ProductModifiers\Modifiers\ValueObjects\ModifierSelected;
use Gambio\Shop\ProductModifiers\Presentation\Core\PresentationInfoInterface;
use Gambio\Shop\ProductModifiers\Modifiers\ValueObjects\AdditionalInfo;

/**
 * Class AbstractModifierBuilder
 * @package Gambio\Shop\ProductModifiers\Modifiers\Builders
 */
abstract class AbstractModifierBuilder implements ModifierBuilderInterface
{
    /**
     * @var ModifierIdentifierInterface
     */
    protected $id;
    /**
     * @var PresentationInfoInterface
     */
    protected $info;
    /**
     * @var ModifierName
     */
    protected $name;
    /**
     * @var string
     */
    protected $source;
    /**
     * @var AdditionalInfo
     */
    protected $additionalInfo;

    /**
     * @var ModifierSelected
     */
    protected $selected;

    /**
     * @var ModifierSelectable
     */
    protected $selectable;

    /**
     * @inheritDoc
     */
    public function build(): ModifierInterface
    {
        $result = $this->createInstance();
        $this->resetFields();

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function withAdditionalInfo(AdditionalInfo $additionalInfo): ModifierBuilderInterface
    {
        $this->additionalInfo = $additionalInfo;

        return $this;
    }

    /**
     * @param ModifierIdentifierInterface $id
     *
     * @return AbstractModifierBuilder
     */
    public function withId(ModifierIdentifierInterface $id): ModifierBuilderInterface
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function withName(ModifierName $name): ModifierBuilderInterface
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @param PresentationInfoInterface $info
     *
     * @return AbstractModifierBuilder
     */
    public function withValue(PresentationInfoInterface $info): ModifierBuilderInterface
    {
        $this->info = $info;

        return $this;
    }


    /**
     * @param ModifierSelected $selected
     *
     * @return AbstractModifierBuilder
     */
    public function withSelected(ModifierSelected $selected): ModifierBuilderInterface
    {
        $this->selected = $selected;

        return $this;
    }

    /**
     * @param ModifierSelectable $selectable
     *
     * @return AbstractModifierBuilder
     */
    public function withSelectable(ModifierSelectable $selectable): ModifierBuilderInterface
    {
        $this->selectable = $selectable;

        return $this;
    }

    /**
     * @return ModifierInterface
     */
    abstract protected function createInstance(): ModifierInterface;


    /**
     * clear fields
     */
    protected function resetFields(): void
    {
        $this->id = null;
        $this->name = null;
        $this->source = null;
        $this->info = null;
        $this->selected = null;
        $this->selectable = null;
    }

}