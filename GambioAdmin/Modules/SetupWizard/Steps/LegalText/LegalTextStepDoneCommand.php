<?php
/*--------------------------------------------------------------
   LegalTextStepDoneCommand.php 2020-08-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\SetupWizard\Steps\LegalText;
use Gambio\Admin\Modules\SetupWizard\Commands\Done\AbstractStepDoneCommand;

class LegalTextStepDoneCommand extends AbstractStepDoneCommand
{
    /**
     * LegalTextStepDoneCommand constructor.
     *
     * @param LegalTextStepIsDoneStorage $storage
     */
    public function __construct(LegalTextStepIsDoneStorage $storage)
    {
        parent::__construct($storage);
    }
}