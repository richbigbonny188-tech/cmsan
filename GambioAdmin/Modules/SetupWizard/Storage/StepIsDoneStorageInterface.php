<?php
/* --------------------------------------------------------------
  StepIsDoneStorageInterface.inc.php 2019-05-27
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\SetupWizard\Storage;

use Gambio\Admin\Modules\SetupWizard\ValueObjects\Status;

/**
 * Class StepIsDoneStorageInterface
 */
interface StepIsDoneStorageInterface
{
    public function getValue(): Status;
    
    
    public function setStepComplete(): void;
    
    
    public function setStepIncomplete(): void;
}