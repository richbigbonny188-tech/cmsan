<?php
/* --------------------------------------------------------------
   OptionApiRequestParser.php 2020-08-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Api\Modules\Option\App;

use Exception;
use Gambio\Admin\Modules\Option\Model\ValueObjects\OptionDetail;
use Gambio\Admin\Modules\Option\Model\ValueObjects\OptionValueDetail;
use Gambio\Admin\Modules\Option\Services\OptionFactory;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class OptionApiRequestParser
 *
 * @package Gambio\Api\Modules\Option\App
 */
class OptionApiRequestParser
{
    /**
     * @var OptionFactory
     */
    private $factory;
    
    
    /**
     * OptionApiRequestParser constructor.
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
        $newOptionValues = [];
        
        foreach ($request->getParsedBody() as $index => $optionData) {
            try {
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
     * @param array $detailData
     *
     * @return OptionDetail
     */
    private function mapOptionDetail(array $detailData): OptionDetail
    {
        return $this->factory->createOptionDetail($detailData['languageCode'],
                                                  $detailData['label'],
                                                  $detailData['adminLabel'],
                                                  $detailData['description']);
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