<?php
/*--------------------------------------------------------------
   UpdateOptionValuesSortingOrderAction.php 2022-10-17
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Option\App\Actions\Json;

use Exception;
use Gambio\Admin\Modules\Option\Services\Exceptions\OptionDoesNotExistException;
use Gambio\Admin\Modules\Option\Services\OptionFactory;
use Gambio\Admin\Modules\Option\Services\OptionReadService;
use Gambio\Admin\Modules\Option\Services\OptionWriteService;
use Gambio\Admin\Modules\Option\App\OptionRequestValidator;
use Gambio\Core\Application\Http\AbstractAction;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;

/**
 * Class UpdateOptionValuesSortingOrderAction
 *
 * @package Gambio\Admin\Modules\Option\App\Actions\Json
 */
class UpdateOptionValuesSortingOrderAction extends AbstractAction
{
    /**
     * @var OptionRequestValidator
     */
    private OptionRequestValidator $requestValidator;
    
    /**
     * @var OptionWriteService
     */
    private OptionWriteService $writeService;
    
    /**
     * @var OptionReadService
     */
    private OptionReadService $readService;
    
    /**
     * @var OptionFactory
     */
    private OptionFactory $factory;
    
    
    /**
     * UpdateOptionValuesSortingOrderAction constructor.
     *
     * @param OptionRequestValidator $requestValidator
     * @param OptionWriteService     $writeService
     * @param OptionReadService      $readService
     * @param OptionFactory          $factory
     */
    public function __construct(
        OptionRequestValidator $requestValidator,
        OptionWriteService     $writeService,
        OptionReadService      $readService,
        OptionFactory          $factory
    ) {
        $this->requestValidator = $requestValidator;
        $this->writeService     = $writeService;
        $this->readService      = $readService;
        $this->factory          = $factory;
    }
    
    
    /**
     * @inheritDoc
     */
    public function handle(Request $request, Response $response): Response
    {
        $parsedBody = $request->getParsedBody();
        $errors     = $this->requestValidator->validateOptionValuePatchRequestBody($parsedBody);
        
        if (count($errors) > 0) {
            
            return $response->withStatus(400)->withJson(['errors' => $errors]);
        }
        
        try {
            $optionId     = (int)$request->getAttribute('optionId');
            $option       = $this->readService->getOptionById($optionId);
            $optionValues = $option->values();
        } catch (OptionDoesNotExistException $exception) {
            return $response->withStatus(404);
        }
        
        foreach ($parsedBody as $index => ['id' => $id, 'sortOrder' => $sortOrder]) {
            
            try {
                $optionValueId = $this->factory->createOptionValueId((int)$id);
                $optionValue   = $optionValues->getById($optionValueId);
                
                if ($optionValue !== null) {
                    
                    $option->changeValues($optionValue->withSortOrder((int)$sortOrder));
                }
            } catch (Exception $exception) {
                $errors[$index][] = $exception->getMessage();
            }
        }
        
        if (count($errors) > 0) {
            
            return $response->withStatus(422)->withJson(['errors' => $errors]);
        }
        
        try {
            
            $this->writeService->storeOptions($option);
            
            return $response->withStatus(204);
        } catch (Exception $exception) {
            return $response->withStatus(400)->withJson(['errors' => $exception->getMessage()]);
        }
    }
}