<?php
/*--------------------------------------------------------------
   ProductImageInUseServiceInterface.php 2020-06-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);


/**
 * Interface ProductImageInUseServiceInterface
 */
interface ProductImageInUseServiceInterface
{
    /**
     * @param string $fileName
     *
     * @return bool
     */
    public function imageIsInUse(string $fileName): bool;
}