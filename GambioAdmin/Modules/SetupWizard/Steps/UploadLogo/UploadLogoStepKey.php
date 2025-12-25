<?php
/*--------------------------------------------------------------
   UploadLogoStepKey.php 2020-08-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\SetupWizard\Steps\UploadLogo;

use Gambio\Admin\Modules\SetupWizard\ValueObjects\Key;

/**
 * Class UploadLogoStepKey
 * @package Gambio\Admin\Modules\SetupWizard\Steps\UploadLogo
 */
class UploadLogoStepKey extends Key
{
    /**
     * UploadLogoStepKey constructor.
     */
    public function __construct()
    {
        parent::__construct('SETUP_WIZARD_STEP_LOGO');
    }
}