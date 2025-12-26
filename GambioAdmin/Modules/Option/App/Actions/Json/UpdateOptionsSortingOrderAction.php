<?php
/*--------------------------------------------------------------------------------------------------
    UpdateOptionsSortingOrderAction.php 2023-04-24
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2023 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Modules\Option\App\Actions\Json;

use Exception;
use Gambio\Admin\Modules\Option\App\OptionRequestValidator;
use Gambio\Admin\Modules\Option\Services\Exceptions\OptionDoesNotExistException;
use Gambio\Admin\Modules\Option\Services\OptionReadService;
use Gambio\Admin\Modules\Option\Services\OptionWriteService;
use Gambio\Core\Application\Http\AbstractAction;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;

/**
 *
 */
class UpdateOptionsSortingOrderAction extends AbstractAction
{
    
    /**
     * @var OptionRequestValidator
     */
    private OptionRequestValidator $requestValidator;
    
    
    /**
     * @var OptionReadService
     */
    private OptionReadService $readService;
    
    
    /**
     * @var OptionWriteService
     */
    private OptionWriteService $writeService;
    
    
    /**
     * @param OptionRequestValidator $requestValidator
     * @param OptionReadService      $readService
     * @param OptionWriteService     $writeService
     */
    public function __construct(
        OptionRequestValidator $requestValidator,
        OptionReadService      $readService,
        OptionWriteService     $writeService
    ) {
        $this->requestValidator = $requestValidator;
        $this->readService      = $readService;
        $this->writeService     = $writeService;
    }
    
    
    /**
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     * @throws OptionDoesNotExistException
     */
    public function handle(Request $request, Response $response): Response
    {
        $parsedBody = $request->getParsedBody();
        $errors     = $this->requestValidator->validateOptionPatchRequestBody($parsedBody);
        
        if (count($errors) > 0) {
            return $response->withStatus(400)->withJson(['errors' => $errors]);
        }
        
        $options = [];
        foreach ($parsedBody as $index => ['id' => $id, 'sortOrder' => $sortOrder]) {
            
            try {
                $option = $this->readService->getOptionById($id);
                $option->changeSortOrder((int)$sortOrder);
                $options[] = $option;
            } catch (Exception $exception) {
                $errors[$index][] = $exception->getMessage();
            }
        }
        
        if (count($errors) > 0) {
            return $response->withStatus(422)->withJson(['errors' => $errors]);
        }
        
        try {
            $this->writeService->storeOptionsSortOrder(...$options);
            
            return $response->withStatus(204);
        } catch (Exception $exception) {
            return $response->withStatus(422)->withJson(['errors' => [$exception->getMessage()]]);
        }
    }
}