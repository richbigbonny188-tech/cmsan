<?php
/*--------------------------------------------------------------
   CreateOptionValuesAction.php 2022-10-17
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
use Gambio\Admin\Modules\Option\Model\Option;
use Gambio\Admin\Modules\Option\Services\Exceptions\OptionDoesNotExistException;
use Gambio\Admin\Modules\Option\Services\OptionReadService;
use Gambio\Admin\Modules\Option\Services\OptionWriteService;
use Gambio\Core\Application\Http\AbstractAction;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class CreateOptionValuesAction
 *
 * @package Gambio\Admin\Modules\Option\App\Actions\Json
 */
class CreateOptionValuesAction extends AbstractAction
{
    private const SORT_ORDER_PROVISIONAL_VALUE = -1;
    
    /**
     * @var OptionRequestParser
     */
    private $requestParser;
    
    /**
     * @var OptionRequestValidator
     */
    private $requestValidator;
    
    /**
     * @var OptionReadService
     */
    private $readService;
    
    /**
     * @var OptionWriteService
     */
    private $writeService;
    
    /**
     * @var int
     */
    private $highestSortOrder;
    
    
    /**
     * CreateOptionValuesAction constructor.
     *
     * @param OptionRequestParser    $requestParser
     * @param OptionRequestValidator $requestValidator
     * @param OptionReadService      $readService
     * @param OptionWriteService     $writeService
     */
    public function __construct(
        OptionRequestParser    $requestParser,
        OptionRequestValidator $requestValidator,
        OptionReadService      $readService,
        OptionWriteService     $writeService
    ) {
        $this->requestParser    = $requestParser;
        $this->requestValidator = $requestValidator;
        $this->readService      = $readService;
        $this->writeService     = $writeService;
    }
    
    
    /**
     * @inheritDoc
     */
    public function handle(Request $request, Response $response): Response
    {
        $errors = $this->requestValidator->validateOptionValuePostRequestBody($request->getParsedBody());
        if (count($errors) > 0) {
            return $response->withStatus(400)->withJson(['errors' => $errors]);
        }
        
        try {
            $optionId = (int)$request->getAttribute('optionId');
            $option   = $this->readService->getOptionById($optionId);
            
            if ($this->sortOrderMustbeOverwritten($request)) {
                $this->determineHighestSortOrderValue($option);
                
                $request = $this->overwriteSortOrder($request);
            }
            
            $optionValues = $this->requestParser->parseOptionValueDataForCreation($request, $errors);
            
            if (count($errors) > 0) {
                return $response->withStatus(422)->withJson(['errors' => $errors]);
            }
            
            $option->addNewValues(...$optionValues);
            $this->writeService->storeOptions($option);
            
            $option            = $this->readService->getOptionById($optionId); // Load option again to fetch IDs of added values
            $optionValueIds    = [];
            $optionValuesArray = $option->values()->toArray();
            
            // Sorting by the ID
            uasort($optionValuesArray, static function (array $a, array $b): int {
                return $a['id'] <=> $b['id'];
            });
            
            foreach (array_slice($optionValuesArray, -count($optionValues)) as $optionValue) {
                $optionValueIds[] = $optionValue['id'];
            }
            
            return $response->withJson(['data' => $optionValueIds,], 201);
        } catch (OptionDoesNotExistException $exception) {
            return $response->withStatus(404);
        } catch (Exception $exception) {
            return $response->withStatus(422)->withJson(['errors' => [$exception->getMessage()]]);
        }
    }
    
    
    /**
     * @param ServerRequestInterface $request
     *
     * @return bool
     */
    protected function sortOrderMustBeOverwritten(ServerRequestInterface $request): bool
    {
        foreach ($request->getParsedBody() as $optionData) {
            if ($optionData['sortOrder'] === self::SORT_ORDER_PROVISIONAL_VALUE) {
                return true;
            }
        }
        
        return false;
    }
    
    
    /**
     * @param ServerRequestInterface $request
     *
     * @return ServerRequestInterface
     */
    private function overwriteSortOrder(ServerRequestInterface &$request): ServerRequestInterface
    {
        $requestData = [];
        
        foreach ($request->getParsedBody() as $optionData) {
            
            if ($optionData['sortOrder'] === self::SORT_ORDER_PROVISIONAL_VALUE) {
                
                $optionData['sortOrder'] = ++$this->highestSortOrder;
            }
            
            $requestData[] = $optionData;
        }
        
        return $request->withParsedBody($requestData);
    }
    
    
    /**
     *
     */
    private function determineHighestSortOrderValue(Option $option)
    {
        $sortOrder = 0;
        
        foreach ($option->values() as $optionValue) {
            
            if ($optionValue->sortOrder() > $sortOrder) {
                
                $sortOrder = $optionValue->sortOrder();
            }
        }
        
        $this->highestSortOrder = $sortOrder;
    }
}