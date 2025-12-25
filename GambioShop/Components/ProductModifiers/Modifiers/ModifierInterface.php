<?php
/*--------------------------------------------------------------------------------------------------
    ModifierInterface.php 2020-06-10
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
 * Interface ModifierInterface
 * @package Gambio\Shop\ProductModifiers\Modifiers
 */
interface ModifierInterface
{
    /**
     * @return string
     */
    public static function source(): string;
    
    /**
     * @return ModifierIdentifierInterface
     */
    public function id(): ModifierIdentifierInterface;
    
    /**
     * @return ModifierName
     */
    public function name(): ModifierName;
    
    /**
     * @return PresentationInfoInterface
     */
    public function info(): PresentationInfoInterface;

    /**
     * @return AdditionalInfo
     */
    public function additionalInfo() : AdditionalInfo;

    /**
     * @return ModifierSelected|null
     */
    public function selected(): ?ModifierSelected;

    /**
     * @return ModifierSelectable|null
     */
    public function selectable(): ?ModifierSelectable;

}