<?php
/*--------------------------------------------------------------------------------------------------
    ImageFactoryInterface.php 2020-02-17
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Shop\Properties\SellingUnitImages\Database\Repository\Factories;

use Gambio\Shop\Properties\SellingUnitImages\Database\Repository\DTO\ImageDto;
use Gambio\Shop\SellingUnit\Images\Entities\Interfaces\SellingUnitImageInterface;

/**
 * Interface ImageFactoryInterface
 * @package Gambio\Shop\Properties\SellingUnitImages\Database\Repository\Factories
 */
interface ImageFactoryInterface
{
    /**
     * @param ImageDto $dto
     *
     * @return SellingUnitImageInterface
     */
    public function createImage(ImageDto $dto) : SellingUnitImageInterface;
}