<?php
/*--------------------------------------------------------------
   UpdateOptionValuesAction.php 2022-10-17
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
use Gambio\Admin\Modules\Option\Services\Exceptions\OptionDoesNotExistException;
use Gambio\Admin\Modules\Option\Services\OptionFactory;
use Gambio\Admin\Modules\Option\Services\OptionReadService;
use Gambio\Admin\Modules\Option\Services\OptionWriteService;
use Gambio\Core\Application\Http\AbstractAction;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;

/**
 * Class UpdateOptionValuesAction
 *
 * @package Gambio\Admin\Modules\Option\App\Actions\Json
 */
class UpdateOptionValuesAction extends AbstractAction
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
     * UpdateOptionValuesAction constructor.
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
        $errors     = $this->requestValidator->validateOptionValuePutRequestBody($parsedBody);
        
        if (count($errors) > 0) {
            return $response->withStatus(400)->withJson(['errors' => $errors]);
        }
        
        try {
            $optionId = (int)$request->getAttribute('optionId');
            $option   = $this->readService->getOptionById($optionId);
        } catch (OptionDoesNotExistException $exception) {
            return $response->withStatus(404);
        }
        
        $optionValues = [];
        
        foreach ($parsedBody as $index => $optionData) {
            try {
                $id             = $this->factory->createOptionValueId($optionData['id']);
                $details        = $this->requestParser->parseOptionValueDetailsDataForUpdate($optionData['details']);
                $details        = $this->factory->createOptionValueDetails(...array_values($details));
                $productDetails = $this->requestParser->parseOptionValueProductDetailsDataForUpdate($optionData);
                $stock          = $this->factory->createOptionValueStock($optionData['stockType'],
                                                                         $optionData['stock'],
                                                                         $optionData['stockCentrallyManaged']);
                $sortOrder      = $optionData['sortOrder'];
                $image          = $optionData['image'];
                
                $optionValues[] = $this->factory->createOptionValue($id,
                                                                    $details,
                                                                    $productDetails,
                                                                    $stock,
                                                                    $sortOrder,
                                                                    $image);
            } catch (Exception $exception) {
                $errors[$index][] = $exception->getMessage();
                continue;
            }
            
            $originalOptionValue = $option->values()->getById($id);
            
            if ($originalOptionValue === null) {
                $errorMessage = 'Option value id "%s" does not belongs to option id "%s"';
                $errorMessage = sprintf($errorMessage, $id->value(), $option->id());
                
                return $response->withStatus(422)->withJson(['errors' => [$errorMessage]]);
            }
        }
        
        if (count($errors) > 0 || count($optionValues) === 0) {
            return $response->withStatus(422)->withJson(['errors' => $errors]);
        }
        
        try {
            $option->changeValues(...$optionValues);
            $this->writeService->storeOptions($option);
            
            return $response->withStatus(204);
        } catch (Exception $exception) {
            return $response->withStatus(404);
        }
    }
}