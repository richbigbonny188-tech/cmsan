<?php
/*--------------------------------------------------------------------------------------------------
    AbstractModifierIdentifier.php 2020-01-23
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\Shop\ProductModifiers\Modifiers\ValueObjects;

/**
 * Class AbstractModifierIdentifier
 * @package Gambio\Shop\ProductModifiers\Modifiers\ValueObjects
 */
abstract class AbstractModifierIdentifier implements ModifierIdentifierInterface
{

    /**
     * @var mixed
     */
    protected $value;

    /**
     * AbstractModifierIdentifier constructor.
     *
     * @param mixed $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }
    
    
    /**
     * @inheritDoc
     */
    public function equals(ModifierIdentifierInterface $id): bool
    {
        return $id->value() === $this->value() && $id->type() === $this->type() && is_a($id, self::class);
    }


    /**
     * @return mixed
     */
    public function value()
    {
        return $this->value;
    }
    
    
    /**
     * @inheritDoc
     */
    abstract public function type(): string;
}