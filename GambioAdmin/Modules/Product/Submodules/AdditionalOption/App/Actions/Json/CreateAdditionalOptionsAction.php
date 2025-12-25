<?php
/*--------------------------------------------------------------
   CreateAdditionalOptionsAction.php 2023-06-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\AdditionalOption\App\Actions\Json;

use Exception;
use Gambio\Admin\Modules\Option\Model\Entities\OptionValue;
use Gambio\Admin\Modules\Option\Model\ValueObjects\OptionValueId;
use Gambio\Admin\Modules\Option\Services\Exceptions\OptionDoesNotExistException;
use Gambio\Admin\Modules\Option\Services\OptionReadService as OptionReadServiceInterface;
use Gambio\Admin\Modules\Price\Services\ProductPriceConversionService;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\App\AdditionalOptionRequestParser;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\App\AdditionalOptionRequestValidator;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects\OptionAndOptionValueId;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects\OptionValueCustomization;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects\ProductId;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services\AdditionalOptionReadService as AdditionalOptionReadServiceInterface;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services\AdditionalOptionWriteService as AdditionalOptionWriteServiceInterface;
use Gambio\Core\Application\Http\AbstractAction;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;

/**
 * Class CreateAdditionalOptionsAction
 *
 * @package Gambio\Admin\Modules\Product\Submodules\AdditionalOption\App\Actions\Json
 * @codeCoverageIgnore
 */
class CreateAdditionalOptionsAction extends AbstractAction
{
    private const SORT_ORDER_PROVISIONAL_VALUE = -1;
    private int $highestSortOrder;
    
    
    /**
     * CreateAdditionalOptionsAction constructor.
     *
     * @param AdditionalOptionRequestValidator      $validator
     * @param AdditionalOptionRequestParser         $parser
     * @param AdditionalOptionWriteServiceInterface $writeService
     * @param AdditionalOptionReadServiceInterface  $readService
     * @param OptionReadServiceInterface            $optionReadService
     * @param ProductPriceConversionService         $priceModifyService
     */
    public function __construct(
        private AdditionalOptionRequestValidator      $validator,
        private AdditionalOptionRequestParser         $parser,
        private AdditionalOptionWriteServiceInterface $writeService,
        private AdditionalOptionReadServiceInterface  $readService,
        private OptionReadServiceInterface            $optionReadService,
        private ProductPriceConversionService         $priceModifyService
    ) {
    }
    
    
    /**
     * @inheritDoc
     */
    public function handle(Request $request, Response $response): Response
    {
        $errors = $this->validator->validateCreationBody($request->getParsedBody());
        
        if (empty($errors) === false) {
            return $response->withStatus(400)->withJson(['errors' => $errors]);
        }
        
        $creationArguments = $this->parser->parseAdditionalOptionsData($request, $errors);
        
        if (empty($errors) === false || empty($creationArguments) === true) {
            return $response->withStatus(422)->withJson(['errors' => $errors]);
        }
        
        try {
            if ($this->sortOrderMustBeOverwritten(...$creationArguments)) {
                $this->determineHighestSortOrderValue($request);
                $creationArguments = $this->overwriteSortOrder(...$creationArguments);
            }
            
            $this->applyOptionValueCustomization($creationArguments);
            
            $optionIds = $this->writeService->createMultipleAdditionalOptions(...$creationArguments);
            
            return $response->withStatus(200)->withJson(['data' => $optionIds->toArray()]);
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
            if ($arguments[5] === self::SORT_ORDER_PROVISIONAL_VALUE) {
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
            if ($arguments[5] === self::SORT_ORDER_PROVISIONAL_VALUE) {
                $arguments[5] = ++$this->highestSortOrder;
            }
        }
        
        return $creationArguments;
    }
    
    
    /**
     * @param Request $request
     */
    private function determineHighestSortOrderValue(Request $request): void
    {
        $sortOrder      = 0;
        $productId      = (int)$request->getAttribute('productId');
        $productOptions = $this->readService->getAdditionalOptionsByProductId($productId);
        
        foreach ($productOptions as $productOption) {
            if ($productOption->sortOrder() > $sortOrder) {
                $sortOrder = $productOption->sortOrder();
            }
        }
        
        $this->highestSortOrder = $sortOrder;
    }
    
    
    /**
     * @throws OptionDoesNotExistException
     * @noinspection MultiAssignmentUsageInspection
     */
    private function applyOptionValueCustomization(array &$creationArguments): void
    {
        foreach ($creationArguments as &$arguments) {
            /**
             * @var ProductId $productId
             */
            $productId = $arguments[0];
            /** @var OptionAndOptionValueId $optionAndOptionValueId */
            $optionAndOptionValueId = $arguments[1];
            /** @var OptionValueCustomization $customization */
            $customization = &$arguments[3];
            
            $optionValue   = $this->optionValueById($optionAndOptionValueId);
            $customization = $customization->withPrice($this->priceModifyService->getNetPrice($optionValue->price()
                                                                                              !== 0.0 ? $optionValue->price() : $customization->price(),
                                                                                              $productId->value()));
            
            if ($customization->weight() === 0.0 && $optionValue->weight() !== 0.0) {
                $customization = $customization->withWeight($optionValue->weight());
            }
            
            if (empty($customization->modelNumber()) && !empty($optionValue->modelNumber())) {
                $customization = $customization->withModelNumber($optionValue->modelNumber());
            }
        }
    }
    
    
    /**
     * @param OptionAndOptionValueId $optionAndOptionValueId
     *
     * @return OptionValue
     * @throws OptionDoesNotExistException
     */
    protected function optionValueById(OptionAndOptionValueId $optionAndOptionValueId): OptionValue
    {
        return $this->optionReadService->getOptionById($optionAndOptionValueId->optionId())
            ->values()
            ->getById(OptionValueId::create($optionAndOptionValueId->optionValueId()));
    }
}