<?php
/*--------------------------------------------------------------
   UploadLogoReader.php 2020-08-17
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\SetupWizard\Steps\UploadLogo\Reader;

use Gambio\Admin\Modules\SetupWizard\Steps\UploadLogo\DTO\CurrentLogoDTO;

/**
 * Interface UploadLogoReader
 * @package Gambio\Admin\Modules\SetupWizard\Steps\UploadLogo\Reader
 */
interface UploadLogoReader
{
    /**
     * @return CurrentLogoDTO
     */
    public function currentLogo(): CurrentLogoDTO;
}