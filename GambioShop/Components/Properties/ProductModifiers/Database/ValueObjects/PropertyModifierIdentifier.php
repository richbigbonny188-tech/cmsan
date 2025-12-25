<?php
/*--------------------------------------------------------------------------------------------------
    PropertyModifierIdentifier.php 2020-06-10
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */
declare(strict_types=1);

namespace Gambio\Shop\Properties\ProductModifiers\Database\ValueObjects;

use Gambio\Shop\ProductModifiers\Modifiers\ValueObjects\AbstractModifierIdentifier;
use InvalidArgumentException;

/**
 * Class PropertyModifierIdentifier
 * @package Gambio\Shop\Properties\ProductModifiers\Database\ValueObjects
 */
class PropertyModifierIdentifier extends AbstractModifierIdentifier
{
    /**
     * AbstractModifierIdentifier constructor.
     *
     * @param mixed $value
     */
    public function __construct(int $value)
    {
        if($value === 0)
            throw new InvalidArgumentException('PropertyModifierIdentifier must be bigger than 0!');
        parent::__construct((int)$value);
    }
    /**
     * @inheritDoc
     */
    public function type(): string
    {
        return 'property';
    }
    
    
    /**
     * @return int
     */
    public function value() :int
    {
        return (int)parent::value();
    }
}