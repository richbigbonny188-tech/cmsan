<?php
/*------------------------------------------------------------------------------
 SetupWizardStep.php 2020-08-19
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -----------------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\SetupWizard\Interfaces;

use Gambio\Admin\Modules\SetupWizard\ValueObjects\Icon;
use Gambio\Admin\Modules\SetupWizard\ValueObjects\Index;
use Gambio\Admin\Modules\SetupWizard\ValueObjects\Key;
use Gambio\Admin\Modules\SetupWizard\ValueObjects\Status;
use Gambio\Admin\Modules\SetupWizard\ValueObjects\Text;
use Gambio\Admin\Modules\SetupWizard\ValueObjects\Title;
use Gambio\Admin\Modules\SetupWizard\ValueObjects\Url;
use JsonSerializable;

/**
 * Interface SetupWizardStep
 * @package Gambio\Admin\Modules\SetupWizard\Interfaces
 */
interface SetupWizardStep extends JsonSerializable
{
    /**
     * @return Status
     */
    public function status(): Status;
    
    
    /**
     * @return Icon
     */
    public function icon(): Icon;
    
    
    /**
     * @return Index
     */
    public function index(): Index;
    
    
    /**
     * @return Title
     */
    public function headline(): Title;
    
    
    /**
     * @return Text
     */
    public function description(): Text;
    
    
    /**
     * @return Url
     */
    public function link(): Url;
    
    
    /**
     * @return Key
     */
    public function stepKey(): Key;
    
}