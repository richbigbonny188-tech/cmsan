<?php
/*--------------------------------------------------------------------------------------------------
    AbstractGroupIdentifier.php 2020-01-23
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\Shop\ProductModifiers\Groups\ValueObjects;

/**
 * Class AbstractGroupIdentifier
 * @package Gambio\Shop\ProductModifiers\Groups\ValueObjects
 */
abstract class AbstractGroupIdentifier implements GroupIdentifierInterface
{
    /**
     * @var string
     */
    protected $id;
    
    
    /**
     * AbstractGroupIdentifier constructor.
     *
     * @param int $id
     */
    public function __construct(int $id)
    {
        $this->id     = $id;
    }
    
    
    /**
     * @inheritDoc
     */
    public function equals(?GroupIdentifierInterface $id): bool
    {
        return $id && $id->id() === $this->id() && $id->type() === $this->type();
    }
    
    
    /**
     * @inheritDoc
     */
    public function id(): string
    {
        return $this->id;
    }

}