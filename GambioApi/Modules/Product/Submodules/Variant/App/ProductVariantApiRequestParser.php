<?php
/*--------------------------------------------------------------
   ProductVariantApiRequestParser.php 2023-06-28
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Api\Modules\Product\Submodules\Variant\App;

use Exception;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects\OptionAndOptionValueId;
use Gambio\Admin\Modules\Product\Submodules\Variant\Services\ProductVariantFactory;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class ProductVariantApiRequestParser
 * @package Gambio\Api\Modules\ProductVariant\App
 */
class ProductVariantApiRequestParser
{
    /**
     * @var ProductVariantFactory
     */
    private $factory;
    
    
    /**
     * ProductVariantApiRequestParser constructor.
     *
     * @param ProductVariantFactory $factory
     */
    public function __construct(ProductVariantFactory $factory)
    {
        $this->factory = $factory;
    }
    
    
    /**
     * @param ServerRequestInterface $request
     *
     * @return int
     */
    public function getPerPage(ServerRequestInterface $request): int
    {
        return (int)$request->getQueryParam('per-page', 25);
    }
    
    
    /**
     * @param ServerRequestInterface $request
     *
     * @return int
     */
    public function getPage(ServerRequestInterface $request): int
    {
        return (int)$request->getQueryParam('page', 1);
    }
    
    
    /**
     * @param ServerRequestInterface $request
     *
     * @return int
     */
    public function getLimit(ServerRequestInterface $request): int
    {
        return (int)$request->getQueryParam('limit', 100);
    }
    
    
    /**
     * @param ServerRequestInterface $request
     *
     * @return int
     */
    public function getOffset(ServerRequestInterface $request): int
    {
        return (int)$request->getQueryParam('offset', 0);
    }
    
    
    /**
     * @param ServerRequestInterface $request
     *
     * @return array
     */
    public function getFields(ServerRequestInterface $request): array
    {
        $fields = $request->getQueryParam('fields');
        
        return $fields === null ? [] : explode(',', $fields);
    }
    
    
    /**
     * @param ServerRequestInterface $request
     *
     * @return string
     */
    public function getResourceUrlFromRequest(ServerRequestInterface $request): string
    {
        return $request->getUri()->getScheme() . '://' . $request->getUri()->getHost() . $request->getUri()->getPath();
    }
    
    
    /**
     * @param ServerRequestInterface $request
     *
     * @return array
     */
    public function getFilters(ServerRequestInterface $request): array
    {
        return $request->getQueryParam('filter', []);
    }
    
    
    /**
     * @param ServerRequestInterface $request
     *
     * @return string|null
     */
    public function getSorting(ServerRequestInterface $request): ?string
    {
        return $request->getQueryParam('sort');
    }
    
    
    /**
     * @param ServerRequestInterface $request
     * @param array                  $errors
     *
     * @return array
     */
    public function parseProductVariantsData(
        ServerRequestInterface $request,
        array &$errors = []
    ): array {
        
        $creationArguments = [];
        $productId         = (int)$request->getAttribute('productId');
        
        foreach ($request->getParsedBody() as $index => $documentData) {
            
            try {
                
                $creationArguments[] = $this->getCreationDataFromDocumentBody($documentData, $productId);
            } catch (Exception $exception) {
                
                $errors[$index][] = $exception->getMessage();
            }
        }
        
        return $creationArguments;
    }
    
    
    /**
     * @param ServerRequestInterface $request
     * @param array                  $errors
     *
     * @return array
     */
    public function parseChangedSortOrder(
        ServerRequestInterface $request,
        &$errors = []
    ): array {
        
        $changedSortOrderIds = [];
        $parsedBody          = $request->getParsedBody();
        
        if (is_array($parsedBody)) {
            
            foreach ($parsedBody as $documentData) {
                
                $changedSortOrderIds[] = [
                    'id'        => $documentData['id'],
                    'sortOrder' => (int)$documentData['sortOrder']
                ];
            }
        }
        
        return $changedSortOrderIds;
    }
    
    
    /**
     * @param array $documentData
     * @param int   $productId
     *
     * @return array
     */
    protected function getCreationDataFromDocumentBody(array $documentData, int $productId): array
    {
        $factory                      = $this->factory;
        $optionAndOptionValueIds      = array_map(static function (array $data) use ($factory): OptionAndOptionValueId {
            return $factory->createOptionAndOptionValueId((int)$data['optionId'], (int)$data['optionValueId']);
        },
            $documentData['combination']);
        $productCustomization         = $factory->createProductCustomization($documentData['deliveryTimeId'],
                                                                             $documentData['priceType'],
                                                                             $documentData['price'],
                                                                             $documentData['weightType'],
                                                                             $documentData['weight'],
                                                                             $documentData['vpeScalarValue'],
                                                                             $documentData['vpeUnitId']);
        $productIdentificationNumbers = $factory->createProductIdentificationNumbers($documentData['modelNumber'],
                                                                                     $documentData['EAN'],
                                                                                     $documentData['GTIN'],
                                                                                     $documentData['ASIN']);
    
        return [
            $productId,
            $factory->createOptionAndOptionValueIds(...$optionAndOptionValueIds),
            $documentData['imageListId'],
            $productCustomization,
            $productIdentificationNumbers,
            $factory->createProductVariantStock((float)$documentData['stock'], $documentData['stockType']),
            (int)$documentData['sortOrder']
        ];
    }
    
    
    /**
     * @param array $documentData
     *
     * @return array
     */
    public function parseAddOptionValuesFromDocumentData(array $documentData): array
    {
        $result = [];
    
        foreach ($documentData as ['optionId' => $optionId, 'optionValueId' => $optionValueId]) {
            
            $result[$optionId][] = $optionValueId;
        }
        
        return $result;
    }
}