<?php
/* --------------------------------------------------------------
  AttributeIdDtoInterface.php 2020-03-30
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2020 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\ProductImageList\ReadService\Interfaces;


/**
 * Interface AttributeIdDtoInterface
 * @package Gambio\ProductImageList\ReadService\Interfaces
 */
interface AttributeIdDtoInterface
{
    /**
     * @return int
     */
    public function attributeId(): int;

}