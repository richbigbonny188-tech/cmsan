<?php
/*--------------------------------------------------------------
   UpdateOptionsAction.php 2022-10-17
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Option\App\Actions\Json;

use Exception;
use Gambio\Admin\Modules\Option\App\OptionRequestParser;
use Gambio\Admin\Modules\Option\App\OptionRequestValidator;
use Gambio\Admin\Modules\Option\Services\OptionFactory;
use Gambio\Admin\Modules\Option\Services\OptionReadService;
use Gambio\Admin\Modules\Option\Services\OptionWriteService;
use Gambio\Core\Application\Http\AbstractAction;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;

/**
 * Class UpdateOptionsAction
 *
 * @package Gambio\Admin\Modules\Option\App\Actions\Json
 */
class UpdateOptionsAction extends AbstractAction
{
    /**
     * @var OptionRequestParser
     */
    private OptionRequestParser $requestParser;
    
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
     * UpdateOptionsAction constructor.
     *
     * @param OptionRequestParser    $requestParser
     * @param OptionRequestValidator $requestValidator
     * @param OptionWriteService     $writeService
     * @param OptionReadService      $readService
     * @param OptionFactory          $factory
     */
    public function __construct(
        OptionRequestParser    $requestParser,
        OptionRequestValidator $requestValidator,
        OptionWriteService     $writeService,
        OptionReadService      $readService,
        OptionFactory          $factory
    ) {
        $this->requestParser    = $requestParser;
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
        $errors     = $this->requestValidator->validateOptionPutRequestBody($parsedBody);
        if (count($errors) > 0) {
            
            return $response->withStatus(400)->withJson(['errors' => $errors]);
        }
        
        $options = [];
        foreach ($parsedBody as $index => $optionData) {
            
            try {
                $details   = $this->requestParser->parseOptionDetailsDataForUpdate($optionData['details']);
                $type      = $this->factory->createOptionType($optionData['type']);
                $sortOrder = (int)$optionData['sortOrder'];
                
                $option = $this->readService->getOptionById($optionData['id']);
                $option->changeDetails($this->factory->createOptionDetails(...array_values($details)));
                $option->changeType($type);
                $option->changeSortOrder($sortOrder);
                $options[] = $option;
            } catch (Exception $exception) {
                $errors[$index][] = $exception->getMessage();
            }
        }
        
        if (count($errors) > 0) {
            
            return $response->withStatus(422)->withJson(['errors' => $errors]);
        }
        
        try {
            $this->writeService->storeOptions(...$options);
            
            return $response->withStatus(204);
        } catch (Exception $exception) {
            return $response->withStatus(422)->withJson(['errors' => [$exception->getMessage()]]);
        }
    }
}