<?php
/* --------------------------------------------------------------
   OptionRequestParser.php 2022-10-21
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Option\App;

use Exception;
use Gambio\Admin\Modules\Option\Model\ValueObjects\OptionDetail;
use Gambio\Admin\Modules\Option\Model\ValueObjects\OptionValueDetail;
use Gambio\Admin\Modules\Option\Model\ValueObjects\OptionValuesProductDetails;
use Gambio\Admin\Modules\Option\Services\OptionFactory;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class OptionRequestParser
 *
 * @package Gambio\Admin\Modules\Option\App
 */
class OptionRequestParser
{
    /**
     * @var OptionFactory
     */
    private OptionFactory $factory;
    
    
    /**
     * OptionRequestParser constructor.
     *
     * @param OptionFactory $factory
     */
    public function __construct(OptionFactory $factory)
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
     * @return array
     */
    public function getFields(ServerRequestInterface $request): array
    {
        $fields = $request->getQueryParam('fields');
        
        return ($fields === null) ? [] : explode(',', $fields);
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
     *
     * @return string
     */
    public function getResourceUrlFromRequest(ServerRequestInterface $request): string
    {
        return $request->getUri()->getScheme() . '://' . $request->getUri()->getHost() . $request->getUri()->getPath();
    }
    
    
    /**
     * @param ServerRequestInterface $request
     * @param array                  $errors
     *
     * @return array
     */
    public function parseOptionDataForCreation(ServerRequestInterface $request, &$errors = []): array
    {
        $creationArguments = [];
        
        foreach ($request->getParsedBody() as $index => $optionData) {
            try {
                $details   = array_map([$this, 'mapOptionDetail'], $optionData['details']);
                $details   = $this->factory->createOptionDetails(...array_values($details));
                $newValues = $this->factory->createNewOptionValues();
                $type      = $this->factory->createOptionType($optionData['type']);
                $sortOrder = (int)$optionData['sortOrder'];
                
                $creationArguments[] = [$details, $newValues, $type, $sortOrder];
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
    public function parseOptionValueDataForCreation(ServerRequestInterface $request, &$errors = []): array
    {
        $newOptionValues   = [];
        
        foreach ($request->getParsedBody() as $index => $optionData) {
            try {
                $details        = array_map([$this, 'mapOptionValueDetail'], $optionData['details']);
                $details        = $this->factory->createOptionValueDetails(...array_values($details));
                $productDetails = $this->factory->createOptionValuesProductDetails(strip_tags($optionData['modelNumber']),
                                                                                   $optionData['weight'],
                                                                                   $optionData['price']);
                $stock          = $this->factory->createOptionValueStock($optionData['stockType'],
                                                                         $optionData['stock'],
                                                                         $optionData['stockCentrallyManaged']);
                $sortOrder      = $optionData['sortOrder'];
                $image          = $optionData['image'];
                
                $newOptionValues[] = $this->factory->createNewOptionValue($details,
                                                                          $productDetails,
                                                                          $stock,
                                                                          $sortOrder,
                                                                          $image);
            } catch (Exception $exception) {
                $errors[$index][] = $exception->getMessage();
            }
        }
        
        return $newOptionValues;
    }
    
    
    /**
     * @param array $optionDetailsData
     *
     * @return array
     */
    public function parseOptionDetailsDataForUpdate(array $optionDetailsData): array
    {
        return array_map([$this, 'mapOptionDetail'], $optionDetailsData);
    }
    
    
    /**
     * @param array $optionValueDetailsData
     *
     * @return array
     */
    public function parseOptionValueDetailsDataForUpdate(array $optionValueDetailsData): array
    {
        return array_map([$this, 'mapOptionValueDetail'], $optionValueDetailsData);
    }
    
    
    /**
     * @param array $optionValueData
     *
     * @return OptionValuesProductDetails
     */
    public function parseOptionValueProductDetailsDataForUpdate(array $optionValueData): OptionValuesProductDetails
    {
        return $this->factory->createOptionValuesProductDetails(
            strip_tags($optionValueData['modelNumber']),
            $optionValueData['weight'],
            $optionValueData['price']
        );
    }
    
    
    /**
     * @param array $detailData
     *
     * @return OptionDetail
     */
    private function mapOptionDetail(array $detailData): OptionDetail
    {
        return $this->factory->createOptionDetail($detailData['languageCode'],
                                                  strip_tags($detailData['label']),
                                                  strip_tags($detailData['adminLabel']),
                                                  strip_tags($detailData['description']));
    }
    
    
    /**
     * @param array $valueDetailData
     *
     * @return OptionValueDetail
     */
    private function mapOptionValueDetail(array $valueDetailData): OptionValueDetail
    {
        return $this->factory->createOptionValueDetail($valueDetailData['languageCode'],
                                                       strip_tags($valueDetailData['label']),
                                                       strip_tags($valueDetailData['description']));
    }
    
}