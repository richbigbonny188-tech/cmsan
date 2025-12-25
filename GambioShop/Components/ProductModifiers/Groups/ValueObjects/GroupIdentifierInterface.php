<?php
/*--------------------------------------------------------------------------------------------------
    GroupIdentifierInterface.php 2020-01-23
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\Shop\ProductModifiers\Groups\ValueObjects;

/**
 * Interface GroupIdentifierInterface
 * @package Gambio\Shop\ProductModifiers\Groups\ValueObjects
 */
interface GroupIdentifierInterface
{
    /**
     * @return string
     */
    public function id(): string;
    
    
    /**
     * @param GroupIdentifierInterface $id
     *
     * @return bool
     */
    public function equals(?GroupIdentifierInterface $id): bool;
    
    
    /**
     * @return string
     */
    public function type() : string;
}