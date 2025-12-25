<?php
/*--------------------------------------------------------------
   UploadLogoStepDoneCommand.php 2020-08-17
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\SetupWizard\Commands\Done;


use Gambio\Admin\Modules\SetupWizard\Steps\UploadLogo\UploadLogoStepIsDoneStorage;

/**
 * Class UploadLogoStepDoneCommand
 * @package Gambio\Admin\Modules\SetupWizard\Commands\Done
 */
class UploadLogoStepDoneCommand extends AbstractStepDoneCommand
{
    /**
     * UploadLogoStepDoneCommand constructor.
     *
     * @param UploadLogoStepIsDoneStorage $storage
     */
    public function __construct(UploadLogoStepIsDoneStorage $storage)
    {
        parent::__construct($storage);
    }
}