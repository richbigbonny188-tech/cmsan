<?php
/*--------------------------------------------------------------
   FetchAllCustomerReviewsActions.php 2022-09-28
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\Reviews\App\Actions\JSON;

use Gambio\Admin\Modules\Customer\App\CustomerProductRepository;
use Gambio\Admin\Modules\Customer\Submodules\Reviews\Services\CustomerReviewReadService as CustomerReviewReadServiceInterface;
use Gambio\Admin\Modules\Customer\Submodules\Reviews\Services\Exceptions\ReviewDoesNotExistException;
use Gambio\Core\Application\Http\AbstractAction;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;
use Gambio\Core\Application\ValueObjects\UserPreferences;

/**
 * Class FetchAllCustomerReviewsActions
 *
 * @package Gambio\Admin\Modules\Customer\Submodules\Reviews\App\Actions\JSON
 * @codeCoverageIgnore
 */
class FetchAllCustomerReviewsActions extends AbstractAction
{
    private CustomerReviewReadServiceInterface $service;
    private CustomerProductRepository          $productRepository;
    private int                                $languageId;
    
    
    /**
     * @param CustomerReviewReadServiceInterface $service
     * @param CustomerProductRepository          $productRepository
     * @param UserPreferences                    $preferences
     */
    public function __construct(
        CustomerReviewReadServiceInterface $service,
        CustomerProductRepository          $productRepository,
        UserPreferences                    $preferences
    ) {
        $this->service           = $service;
        $this->productRepository = $productRepository;
        $this->languageId        = $preferences->languageId();
    }
    
    
    /**
     * @inheritDoc
     */
    public function handle(Request $request, Response $response): Response
    {
        try {
            $customerId   = (int)$request->getAttribute('customerId');
            $responseData = $this->service->getCustomerReviews($customerId)->toArray();
            
            $this->addProductInformationToDataArray($responseData);
            
            return $response->withJson($responseData);
        } catch (ReviewDoesNotExistException $exception) {
            return $response->withStatus(404)->withJson(['error' => $exception->getMessage()]);
        }
    }
    
    
    /**
     * @param array $data
     *
     * @return void
     */
    private function addProductInformationToDataArray(array &$data): void
    {
        $productIds  = array_unique(array_column($data, 'productId'));
        $productInfo = $this->productRepository->getProductsNameAndImage($this->languageId, ...$productIds)->toArray();
    
        foreach ($data as &$review) {
            
            foreach ($productInfo as $info) {
                
                if ($info['productId'] === $review['productId']) {
    
                    $review['productName']  = $info['name'];
                    $review['productImage'] = $info['image'];
                }
            }
        }
    }
}