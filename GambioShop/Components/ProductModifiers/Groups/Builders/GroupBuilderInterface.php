<?php
/*--------------------------------------------------------------------------------------------------
    GroupBuilderInterface.php 2020-06-10
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
use Gambio\Shop\ProductModifiers\Modifiers\ModifierInterface;
use Gambio\Shop\ProductModifiers\Presentation\Core\PresentationTypeInterface;

/**
 * Interface GroupBuilderInterface
 * @package Gambio\Shop\ProductModifiers\Groups\Builders
 */
interface GroupBuilderInterface
{
    /**
     * @param GroupIdentifierInterface $id
     *
     * @return AbstractGroupBuilder
     */
    public function withId(GroupIdentifierInterface $id): GroupBuilderInterface;
    
    
    /**
     * @param GroupName $name
     *
     * @return $this
     */
    public function withName(GroupName $name): GroupBuilderInterface;
    
    
    /**
     * @param PresentationTypeInterface $presentationType
     *
     * @return GroupBuilderInterface
     */
    public function withType(PresentationTypeInterface $presentationType): GroupBuilderInterface;
    
    
    /**
     * @return GroupInterface
     * @throws InvalidGroupSourceException
     */
    public function build(): GroupInterface;
    
    
    /**
     * @param ModifierInterface ...$build
     *
     * @return mixed
     */
    public function withModifiers(ModifierInterface ...$build): GroupBuilderInterface;
    
    
    /**
     * @param string $source
     *
     * @return GroupBuilderInterface
     */
    public function withSource(string $source): GroupBuilderInterface;

    /**
     * @param GroupStatus $previous
     *
     * @return GroupBuilderInterface
     */
    public function withStatus(GroupStatus $status): GroupBuilderInterface;
}