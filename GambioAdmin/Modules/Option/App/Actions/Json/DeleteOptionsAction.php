<?php
/*--------------------------------------------------------------
   DeleteOptionsAction.php 2021-08-02
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Option\App\Actions\Json;

use Exception;
use Gambio\Admin\Modules\Option\Services\OptionWriteService;
use Gambio\Core\Application\Http\AbstractAction;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;

/**
 * Class DeleteOptionsAction
 * @package Gambio\Admin\Modules\Option\App\Actions\Json
 */
class DeleteOptionsAction extends AbstractAction
{
    /**
     * @var OptionWriteService
     */
    private $service;
    
    
    /**
     * DeleteOptionsAction constructor.
     *
     * @param OptionWriteService $service
     */
    public function __construct(OptionWriteService $service)
    {
        $this->service = $service;
    }
    
    
    /**
     * @inheritDoc
     */
    public function handle(Request $request, Response $response): Response
    {
        $ids = [];
    
        if ($request->getAttribute('optionIds') !== null) {
            
            $ids = array_map('intval', explode(',', $request->getAttribute('optionIds')));
        }
    
        try {
            $this->service->deleteOptions(...$ids);
            return $response->withStatus(204);
        } catch (Exception $e) {
            return $response->withStatus(409)->withJson(['errors' => [[$e->getMessage()]]]);
        }
    }
}