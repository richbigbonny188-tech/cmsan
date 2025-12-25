<?php
/*--------------------------------------------------------------------------------------------------
    CustomizerModifierIdentifier.php 2020-06-10
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */
declare(strict_types=1);
namespace Gambio\Shop\GxCustomizer\ProductModifiers\Database\ValueObjects;

use Gambio\Shop\ProductModifiers\Modifiers\ValueObjects\AbstractModifierIdentifier;

/**
 * Class CustomizerModifierIdentifier
 * @package Gambio\Shop\GxCustomizer\ProductModifiers\Database\ValueObjects
 */
class CustomizerModifierIdentifier extends AbstractModifierIdentifier
{
    /**
     * @return int
     */
    public function value() : int
    {
        return (int)parent::value();
    }


    /**
     * @inheritDoc
     */
    public function __construct($value)
    {
        $value = ((int)$value);
        parent::__construct((int)$value);
    }


    /**
     * @inheritDoc
     */
    public function type() : string
    {
        return 'customizer';
    }
}