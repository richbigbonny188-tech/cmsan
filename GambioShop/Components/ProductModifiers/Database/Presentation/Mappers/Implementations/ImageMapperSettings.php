<?php
/*--------------------------------------------------------------------------------------------------
    ImageMapperSettings.php 2020-02-06
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */
namespace Gambio\Shop\ProductModifiers\Database\Presentation\Mappers\Implementations;

/**
 * Class ImageMapperSettings
 * @package Gambio\Shop\ProductModifiers\Database\Presentation\Mappers\Implementations
 */
class ImageMapperSettings
{
    /**
     * @return string
     */
    public function imagesUrl() : string {
        return DIR_WS_IMAGES;
    }


    /**
     * @return string
     */
    public function imagesPath(): string
    {
        return DIR_FS_DOCUMENT_ROOT.'images/';
    }
}