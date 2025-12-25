<?php
/*--------------------------------------------------------------
   ProductImageBaseNameFactoryInterface.php 2020-06-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

/**
 * Interface ProductImageBaseNameFactoryInterface
 */
interface ProductImageBaseNameFactoryInterface
{
    /**
     * @param string $filename
     *
     * @return ProductImageBaseName
     */
    public function create(string $filename): ProductImageBaseName;
}