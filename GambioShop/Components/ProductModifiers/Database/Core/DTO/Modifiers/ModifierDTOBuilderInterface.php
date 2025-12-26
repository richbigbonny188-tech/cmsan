<?php
/*--------------------------------------------------------------------------------------------------
    ModifierDTOBuilderInterface.php 2021-03-04
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
 * Interface ModifierDTOBuilderInterface
 * @package Gambio\Shop\ProductModifiers\Database\Core\DTO\Modifiers
 */
interface ModifierDTOBuilderInterface
{
    
    /**
     * @param ModifierIdentifierInterface $id
     *
     * @return ModifierDTOBuilderInterface
     */
    public function withId(ModifierIdentifierInterface $id): ModifierDTOBuilderInterface;
    
    
    /**
     * @param GroupIdentifierInterface $id
     *
     * @return ModifierDTOBuilderInterface
     */
    public function withGroupId(GroupIdentifierInterface $id): ModifierDTOBuilderInterface;
    
    
    /**
     * @param string $source
     *
     * @return ModifierDTOBuilderInterface
     */
    public function withSource(string $source): ModifierDTOBuilderInterface;

    /**
     * @param string $pricePrefix
     * @return ModifierDTOBuilderInterface
     */
    public function withPricePrefix(string $pricePrefix): ModifierDTOBuilderInterface;

    /**
     * @param float $pricePrefix
     * @return ModifierDTOBuilderInterface
     */
    public function withPrice(float $pricePrefix): ModifierDTOBuilderInterface;

    
    /**
     * @param string $name
     *
     * @return ModifierDTOBuilderInterface
     */
    public function withName(string $name): ModifierDTOBuilderInterface;
    
    
    /**
     * @param string $image
     *
     * @return ModifierDTOBuilderInterface
     */
    public function withImage(string $image): ModifierDTOBuilderInterface;
    
    
    /**
     * @param string $type
     *
     * @return ModifierDTOBuilderInterface
     */
    public function withType(string $type): ModifierDTOBuilderInterface;
    
    
    /**
     * @return ModifierDTO
     */
    public function build(): ModifierDTO;

    /**
     * @param bool $selected
     *
     * @return ModifierDTOBuilderInterface
     */
    public function withSelected(bool $selected): ModifierDTOBuilderInterface;

    /**
     * @param bool $selectable
     *
     * @return ModifierDTOBuilderInterface
     */
    public function withSelectable(bool $selectable): ModifierDTOBuilderInterface;
    
    
    /**
     * @param bool $showStock
     *
     * @return ModifierDTOBuilderInterface
     */
    public function withShowStock(bool $showStock): ModifierDTOBuilderInterface;

}
