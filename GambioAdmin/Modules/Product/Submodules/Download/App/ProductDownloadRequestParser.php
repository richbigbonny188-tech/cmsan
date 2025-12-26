<?php
/*--------------------------------------------------------------
   ProductDownloadRequestParser.php 2023-06-26
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\Download\App;

use Exception;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\ValueObjects\OptionValueCustomization;
use Gambio\Admin\Modules\Product\Submodules\Download\Services\ProductDownloadFactory;
use Gambio\Core\Application\Http\Request;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class ProductDownloadRequestParser
 */
class ProductDownloadRequestParser
{
    /**
     * ProductOptionApiRequestParser constructor.
     *
     * @param ProductDownloadFactory $factory
     */
    public function __construct(private ProductDownloadFactory $factory) { }
    
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
     * @param Request $request
     * @param array   $errors
     *
     * @return array
     */
    public function parseProductOptionsData(Request $request, array &$errors): array
    {
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
     * @param array $optionValueCustomizationData
     *
     * @return OptionValueCustomization
     */
    public function parseOptionValueCustomizationData(array $optionValueCustomizationData): OptionValueCustomization
    {
        $parsedModelNumber = strip_tags($optionValueCustomizationData['modelNumber']);
        
        return $this->factory->createOptionValueCustomization($parsedModelNumber,
                                                              $optionValueCustomizationData['weight'],
                                                              $optionValueCustomizationData['price']);
    }
    
    
    /**
     * @param array $documentData
     * @param int   $productId
     *
     * @return array
     */
    private function getCreationDataFromDocumentBody(array $documentData, int $productId): array
    {
        return [
            $this->factory->createProductId($productId),
            $this->factory->createOptionAndOptionValueId($documentData['optionId'], $documentData['optionValueId']),
            $this->factory->createImageListId($documentData['imageListId']),
            $this->factory->createOptionValueCustomization(
                strip_tags($documentData['modelNumber']), $documentData['weight'], $documentData['price']
            ),
            $this->factory->createProductDownloadStock($documentData['stock'], $documentData['stockType']),
            $documentData['sortOrder']
        ];
    }
}