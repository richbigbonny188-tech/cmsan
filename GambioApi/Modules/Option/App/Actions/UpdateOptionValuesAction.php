<?php
/* --------------------------------------------------------------
   UpdateOptionValuesAction.php 2020-06-29
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Api\Modules\Option\App\Actions;

use Exception;
use Gambio\Admin\Modules\Option\Model\ValueObjects\OptionValueDetail;
use Gambio\Admin\Modules\Option\Services\Exceptions\OptionDoesNotExistException;
use Gambio\Admin\Modules\Option\Services\OptionFactory;
use Gambio\Admin\Modules\Option\Services\OptionReadService;
use Gambio\Admin\Modules\Option\Services\OptionWriteService;
use Gambio\Api\Modules\Option\App\OptionApiRequestValidator;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;

/**
 * Class UpdateOptionValuesAction
 *
 * @package Gambio\Api\Modules\Option\App\Actions
 */
class UpdateOptionValuesAction
{
    /**
     * @var OptionApiRequestValidator
     */
    private $requestValidator;
    
    /**
     * @var OptionWriteService
     */
    private $writeService;
    
    /**
     * @var OptionReadService
     */
    private $readService;
    
    /**
     * @var OptionFactory
     */
    private $factory;
    
    
    /**
     * UpdateOptionValuesAction constructor.
     *
     * @param OptionApiRequestValidator $requestValidator
     * @param OptionWriteService        $writeService
     * @param OptionReadService         $readService
     * @param OptionFactory             $factory
     */
    public function __construct(
        OptionApiRequestValidator $requestValidator,
        OptionWriteService $writeService,
        OptionReadService $readService,
        OptionFactory $factory
    ) {
        $this->requestValidator = $requestValidator;
        $this->writeService     = $writeService;
        $this->readService      = $readService;
        $this->factory          = $factory;
    }
    
    
    /**
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     *
     * @return Response
     */
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $errors = $this->requestValidator->validateOptionValuePutRequestBody($request->getParsedBody());
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
        
        foreach ($request->getParsedBody() as $index => $optionData) {
            try {
                $id             = $this->factory->createOptionValueId((int)$optionData['id']);
                $details        = array_map([$this, 'mapOptionValueDetail'], $optionData['details']);
                $details        = $this->factory->createOptionValueDetails(...array_values($details));
                $productDetails = $this->factory->createOptionValuesProductDetails($optionData['modelNumber'],
                                                                                   $optionData['weight'],
                                                                                   $optionData['price']);
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
        } catch (Exception $exception) {
            return $response->withStatus(404);
        }
        
        return $response->withStatus(204);
    }
    
    
    /**
     * @param array $valueDetailData
     *
     * @return OptionValueDetail
     */
    private function mapOptionValueDetail(array $valueDetailData): OptionValueDetail
    {
        return $this->factory->createOptionValueDetail($valueDetailData['languageCode'],
                                                       $valueDetailData['label'],
                                                       $valueDetailData['description']);
    }
}