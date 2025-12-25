<?php
/* --------------------------------------------------------------
   UpdateNow.php 2022-03-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);

namespace GXModules\Gambio\Haendlerbund\Admin\App\Actions;

use Gambio\Admin\Application\Http\AdminModuleAction;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;
use GXModules\Gambio\Haendlerbund\Admin\Classes\Exceptions\BatixServiceException;
use GXModules\Gambio\Haendlerbund\Admin\Classes\Exceptions\LegaltextUpdateException;
use GXModules\Gambio\Haendlerbund\Admin\Classes\HaendlerbundUpdateService;

class UpdateNow extends AdminModuleAction
{
    /**
     * @var HaendlerbundUpdateService
     */
    private $updateService;
    
    
    public function __construct(HaendlerbundUpdateService $updateService)
    {
        $this->updateService = $updateService;
    }
    
    public function handle(Request $request, Response $response): Response
    {
        $message = 'update_success';
        $messageDetail = '';
        
        try {
            $this->updateService->updateLegalTexts();
        } catch (BatixServiceException|LegaltextUpdateException $e) {
            $message = 'update_fail';
            $messageDetail = $e->getMessage();
        }

        $_SESSION['haendlerbund_message'] = [
            'message' => $message,
            'message_detail' => $messageDetail,
        ];
        $showConfigurationUrl = $this->url->admin() . '/haendlerbund/configuration';
        return $response->withRedirect($showConfigurationUrl, 302);
    }
}