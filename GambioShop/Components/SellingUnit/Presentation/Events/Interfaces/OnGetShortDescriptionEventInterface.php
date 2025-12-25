<?php
/**
 * OnGetShortDescriptionEventInterface.php 2020-3-18
 * Gambio GmbH
 * http://www.gambio.de
 * Copyright (c) 2020 Gambio GmbH
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
 */

declare(strict_types=1);

namespace Gambio\Shop\SellingUnit\Presentation\Events\Interfaces;

use Gambio\Shop\SellingUnit\Presentation\ValueObjects\ShortDescription;
use Gambio\Shop\SellingUnit\Unit\SellingUnitInterface;

/**
 * Interface OnGetShortDescriptionEventInterface
 * @package Gambio\Shop\SellingUnit\Presentation\Events\Interfaces
 */
interface OnGetShortDescriptionEventInterface
{
    /**
     * @return SellingUnitInterface
     */
    public function getSellingUnit(): SellingUnitInterface;
    
    /**
     * @param ShortDescription $description
     */
    public function setShortDescription(ShortDescription $description): void;
}