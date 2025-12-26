<?php
/*--------------------------------------------------------------
   CreateOptionsAction.php 2022-10-17
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
use Gambio\Admin\Modules\Option\Services\OptionReadService as OptionReadServiceInterface;
use Gambio\Admin\Modules\Option\Services\OptionWriteService as OptionWriteServiceInterface;
use Gambio\Core\Application\Http\AbstractAction;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;

/**
 * Class CreateOptionsAction
 *
 * @package Gambio\Admin\Modules\Option\App\Actions\Json
 */
class CreateOptionsAction extends AbstractAction
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
     * @var OptionWriteServiceInterface
     */
    private $writeService;
    
    /**
     * @var OptionReadServiceInterface
     */
    private $readService;
    
    /**
     * @var int
     */
    private $highestSortOrder;
    
    
    /**
     * CreateOptionsAction constructor.
     *
     * @param OptionRequestParser         $requestParser
     * @param OptionRequestValidator      $requestValidator
     * @param OptionWriteServiceInterface $writeService
     * @param OptionReadServiceInterface  $readService
     */
    public function __construct(
        OptionRequestParser         $requestParser,
        OptionRequestValidator      $requestValidator,
        OptionWriteServiceInterface $writeService,
        OptionReadServiceInterface  $readService
    ) {
        $this->requestParser    = $requestParser;
        $this->requestValidator = $requestValidator;
        $this->writeService     = $writeService;
        $this->readService      = $readService;
    }
    
    
    /**
     * @inheritDoc
     */
    public function handle(Request $request, Response $response): Response
    {
        $errors = $this->requestValidator->validateOptionPostRequestBody($request->getParsedBody());
        if (count($errors) > 0) {
            
            return $response->withStatus(400)->withJson(['errors' => $errors]);
        }
        
        $creationArguments = $this->requestParser->parseOptionDataForCreation($request, $errors);
        if (count($errors) > 0) {
            
            return $response->withStatus(422)->withJson(['errors' => $errors]);
        }
        
        try {
            if ($this->sortOrderMustBeOverwritten(...$creationArguments)) {
                
                $this->determineHighestSortOrderValue();
                $creationArguments = $this->overwriteSortOrder(...$creationArguments);
            }
            
            $ids = $this->writeService->createMultipleOptions(...$creationArguments);
            
            return $response->withJson(['data' => $ids->toArray()])->withStatus(201);
        } catch (Exception $exception) {
            return $response->withStatus(422)->withJson(['errors' => [$exception->getMessage()]]);
        }
    }
    
    
    /**
     * @param mixed ...$creationArguments
     *
     * @return bool
     */
    protected function sortOrderMustBeOverwritten(array ...$creationArguments): bool
    {
        foreach ($creationArguments as $arguments) {
            
            if ($arguments[3] === self::SORT_ORDER_PROVISIONAL_VALUE) {
                
                return true;
            }
        }
        
        return false;
    }
    
    
    /**
     * @param mixed ...$creationArguments
     *
     * @return array[]
     */
    private function overwriteSortOrder(array ...$creationArguments): array
    {
        foreach ($creationArguments as &$arguments) {
            
            if ($arguments[3] === self::SORT_ORDER_PROVISIONAL_VALUE) {
                
                $arguments[3] = ++$this->highestSortOrder;
            }
        }
        
        return $creationArguments;
    }
    
    
    /**
     *
     */
    private function determineHighestSortOrderValue()
    {
        $sortOrder = 0;
        $options   = $this->readService->getAllOptions();
        
        foreach ($options as $option) {
            
            if ($option->sortOrder() > $sortOrder) {
                
                $sortOrder = $option->sortOrder();
            }
        }
        
        $this->highestSortOrder = $sortOrder;
    }
}