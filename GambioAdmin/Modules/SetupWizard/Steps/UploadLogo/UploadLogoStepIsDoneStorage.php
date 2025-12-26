<?php
/*--------------------------------------------------------------
   UploadLogoStepIsDoneStorage.php 2020-08-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\SetupWizard\Steps\UploadLogo;

use Gambio\Admin\Modules\SetupWizard\Storage\AbstractStepIsDoneStorage;
use Gambio\Core\Configuration\Compatibility\ConfigurationStorageRepositoryBuilder;

/**
 * Class UploadLogoStepIsDoneStorage
 * @package Gambio\Admin\Modules\SetupWizard\Storage
 * @codeCoverageIgnore
 */
class UploadLogoStepIsDoneStorage extends AbstractStepIsDoneStorage
{
    /**
     * UploadLogoStepIsDoneStorage constructor.
     *
     * @param UploadLogoStepKey                     $key
     * @param ConfigurationStorageRepositoryBuilder $builder
     */
    public function __construct(UploadLogoStepKey $key, ConfigurationStorageRepositoryBuilder $builder)
    {
        parent::__construct($key, $builder);
    }
}