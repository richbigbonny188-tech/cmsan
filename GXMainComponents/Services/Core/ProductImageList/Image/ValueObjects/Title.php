<?php
/* --------------------------------------------------------------
  Title.php 2020-01-22
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2020 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\ProductImageList\Image\ValueObjects;

/**
 * Class Title
 * @package Gambio\ProductImageList\Image\ValueObjects
 */
class Title extends AbstractText
{
    /**
     * @inheritDoc
     */
    public function type() : string
    {
        return self::TEXT_TYPE_TITLE;
    }
}