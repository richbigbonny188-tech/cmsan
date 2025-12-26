<?php
/*------------------------------------------------------------------------------
 ActionUpdateStep.php 2020-10-19
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -----------------------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Dashboard\App\Actions;

use Gambio\Admin\Modules\SetupWizard\SetupWizardServiceInterface;
use Gambio\Core\Application\Http\AbstractAction;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response as HttpResponse;

/**
 * Class ActionUpdateStep
 *
 * @package Gambio\Admin\Modules\Dashboard\App\Actions
 * @codeCoverageIgnore
 */
class ActionUpdateStep extends AbstractAction
{
    
    /**
     * @var SetupWizardServiceInterface
     */
    private $setupWizardService;
    
    
    public function handle(Request $request, HttpResponse $response): HttpResponse
    {
        if ($request->getAttribute('status') === 'true' && is_string($request->getAttribute('step'))) {
            $this->setupWizardService->setStepComplete($request->getAttribute('step'));
        } else {
            $this->setupWizardService->setStepIncomplete($request->getAttribute('step'));
        }
        
        return $response;
    }
    
    
    /**
     * ActionUpdateStep constructor.
     *
     * @param SetupWizardServiceInterface $setupWizardService
     */
    public function __construct(
        SetupWizardServiceInterface $setupWizardService
    ) {
        $this->setupWizardService = $setupWizardService;
    }
}