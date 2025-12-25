<?php
/* --------------------------------------------------------------
   ShopInformation.php 2023-11-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\Application\ValueObjects;

/**
 * Class ShopInformation
 *
 * @package Gambio\Core\Application\ValueObjects
 */
class ShopInformation
{
    /**
     * @param bool $isCloud
     * @param bool $hasContract
     */
    public function __construct(private bool $isCloud, private bool $hasContract)
    {
    }
    
    
    /**
     * Returns whether if a shop is Cloud shop or not
     *
     * @return bool
     */
    public function isCloud(): bool
    {
        return $this->isCloud;
    }
    
    
    /**
     * Returns whether if a shop has a contract or not
     *
     * @return bool
     */
    public function hasContract(): bool
    {
        return $this->hasContract;
    }
}