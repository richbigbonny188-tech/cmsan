<?php
/*--------------------------------------------------------------------------------------------------
    AbstractModifier.php 2020-06-10
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\Shop\ProductModifiers\Modifiers;


use Gambio\Shop\ProductModifiers\Modifiers\ValueObjects\ModifierSelectable;
use Gambio\Shop\ProductModifiers\Modifiers\ValueObjects\ModifierIdentifierInterface;
use Gambio\Shop\ProductModifiers\Modifiers\ValueObjects\ModifierName;
use Gambio\Shop\ProductModifiers\Modifiers\ValueObjects\ModifierSelected;
use Gambio\Shop\ProductModifiers\Presentation\Core\PresentationInfoInterface;
use Gambio\Shop\ProductModifiers\Modifiers\ValueObjects\AdditionalInfo;

/**
 * Class AbstractModifier
 * @package Gambio\Shop\ProductModifiers\Modifiers
 */
abstract class AbstractModifier implements ModifierInterface
{
    /**
     * @var AdditionalInfo
     */
    protected $additionalInfo;
    /**
     * @var ModifierIdentifierInterface
     */
    private $id;

    /**
     * @var PresentationInfoInterface
     */
    private $info;

    /**
     * @var ModifierName
     */
    private $name;
    /**
     * @var ModifierSelected|null
     */
    private $selected;
    /**
     * @var ModifierSelectable|null
     */
    private $selectable;


    /**
     * AbstractModifier constructor.
     *
     * @param ModifierIdentifierInterface $id
     * @param PresentationInfoInterface $info
     * @param ModifierName $name
     * @param AdditionalInfo $additionalInfo
     * @param ModifierSelected $selected
     * @param ModifierSelectable|null $selectable
     */
    public function __construct(
        ModifierIdentifierInterface $id,
        PresentationInfoInterface $info,
        ModifierName $name,
        AdditionalInfo $additionalInfo,
        ?ModifierSelected $selected,
        ?ModifierSelectable $selectable)
    {
        $this->id = $id;
        $this->info = $info;
        $this->name = $name;
        $this->additionalInfo = $additionalInfo;
        $this->selected = $selected;
        $this->selectable = $selectable;
    }

    /**
     * @inheritDoc
     */
    public function additionalInfo(): AdditionalInfo
    {
        return $this->additionalInfo;
    }

    /**
     * @return ModifierIdentifierInterface
     */
    public function id(): ModifierIdentifierInterface
    {
        return $this->id;
    }

    /**
     * @return PresentationInfoInterface
     */
    public function info(): PresentationInfoInterface
    {
        return $this->info;
    }

    /**
     * @inheritDoc
     */
    public function name(): ModifierName
    {
        return $this->name;
    }

    /**
     * @return ModifierSelected|null
     */
    public function selected(): ?ModifierSelected
    {
        return $this->selected;
    }

    /**
     * @return ModifierSelectable|null
     */
    public function selectable(): ?ModifierSelectable
    {
        return $this->selectable;
    }
}