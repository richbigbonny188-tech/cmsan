<?php
/*--------------------------------------------------------------------------------------------------
    ModifierBuilderInterface.php 2020-06-10
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
 * Interface ModifierBuilderInterface
 * @package Gambio\Shop\ProductModifiers\Modifiers\Builders
 */
interface ModifierBuilderInterface
{

    /**
     * @return ModifierInterface
     */
    public function build(): ModifierInterface;

    /**
     * @param AdditionalInfo $name
     * @return ModifierBuilderInterface
     */
    public function withAdditionalInfo(AdditionalInfo $name): ModifierBuilderInterface;

    /**
     * @param ModifierIdentifierInterface $id
     *
     * @return ModifierBuilderInterface
     */
    public function withId(ModifierIdentifierInterface $id): ModifierBuilderInterface;

    /**
     * @param ModifierName $name
     *
     * @return ModifierBuilderInterface
     */
    public function withName(ModifierName $name): ModifierBuilderInterface;

    /**
     * @param PresentationInfoInterface $info
     *
     * @return ModifierBuilderInterface
     */
    public function withValue(PresentationInfoInterface $info): ModifierBuilderInterface;

    /**
     * @param ModifierSelected $selected
     *
     * @return ModifierBuilderInterface
     */
    public function withSelected(ModifierSelected $selected): ModifierBuilderInterface;

    /**
     * @param ModifierSelectable $selectable
     *
     * @return ModifierBuilderInterface
     */
    public function withSelectable(ModifierSelectable $selectable): ModifierBuilderInterface;
}