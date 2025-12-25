<?php
/* --------------------------------------------------------------
  ProductListingDisplayService.php 2023-12-01
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2023 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

use Gambio\Core\Verification\Service\Exceptions\VerificationExceptionStack;
use Gambio\Core\Verification\Service\VerificationService;
use Gambio\MainComponents\Services\Core\ProductListingDisplayService\Data\DisplayService;
use Gambio\MainComponents\Services\Core\ProductListingDisplayService\Data\FiltersFactory;
use Gambio\MainComponents\Services\Core\ProductListingDisplayService\Data\ProductListingContextFilter;
use Gambio\MainComponents\Services\Core\ProductListingDisplayService\Data\ProductListingVerificationServiceInitializer;
use Gambio\MainComponents\Services\Core\ProductListingDisplayService\Model\ValueObjects\ProductListingControl;
use Gambio\Shop\Modules\ProductListing\Service\ListingFilter;
use Gambio\Shop\Modules\ProductListing\Service\ListingService;

/**
 * Class ProductListingDisplayService
 */
class ProductListingDisplayService implements ProductListingDisplayServiceInterface
{
    private const VERIFICATION_LOG_FILENAME = 'product-listing';
    
    /**
     * @var VerificationService
     */
    private VerificationService $verificationService;
    
    
    /**
     * @param ListingService                               $listing
     * @param DisplayService                               $display
     * @param FiltersFactory                               $filtersFactory
     * @param ProductListingControl                        $control
     * @param ProductListingVerificationServiceInitializer $verificationServiceInitializer ,
     * @param ProductListingContextFilter                  $contextFilter
     */
    public function __construct(
        private ListingService                       $listing,
        private DisplayService                       $display,
        private FiltersFactory                       $filtersFactory,
        private ProductListingControl                $control,
        ProductListingVerificationServiceInitializer $verificationServiceInitializer,
        private ProductListingContextFilter          $contextFilter
    ) {
        $this->verificationService = $verificationServiceInitializer->getService();
    }
    
    
    /**
     * @param array         $original
     * @param ListingFilter $filter
     *
     * @return array
     * @throws Exception
     */
    private function getListing(
        array         $original,
        ListingFilter $filter
    ): array {
        $listing = $this->listing->getListing($filter, $this->control->pagination());
        $data    = $this->display->getListing($listing, $this->control->settings());
        
        return $this->verify($data, $original);
    }
    
    
    /**
     * @param array $data
     * @param array $original
     *
     * @return array
     * @noinspection GlobalVariableUsageInspection
     */
    private function verify(array $data, array $original): array
    {
        [$dataSorted, $originalSorted] = $this->sortArray($data, $original);
        [$dataSorted, $originalSorted] = $this->fixIrrelevantInconsistencies($dataSorted, $originalSorted);
        
        try {
            $this->verificationService->verify($originalSorted, $dataSorted);
        } catch (VerificationExceptionStack $stack) {
            $context = $this->contextFilter->filter($_SESSION);
            $this->verificationService->report($stack, $context, static::VERIFICATION_LOG_FILENAME);
            
            return $original;
        }
        
        return $data;
    }
    
    
    /**
     * Sorts the data by product id. Since some filters sort the products at random intentionally this must be done
     *
     * @param array $data
     * @param array $original
     *
     * @return array
     */
    private function sortArray(array $data, array $original): array
    {
        $key = 'PRODUCTS_ID';
        $cb  = static fn(array $a, array $b): int => $a[$key] <=> $b[$key];
        
        usort($data, $cb);
        usort($original, $cb);
        
        return [$data, $original];
    }
    
    
    /**
     * Some original values are of type string but cause no issues being integer
     *
     * @param array $dataSorted
     * @param array $originalSorted
     *
     * @return array
     */
    private function fixIrrelevantInconsistencies(array $dataSorted, array $originalSorted): array
    {
        $removeShippingLinkLineBreaks = static function ($a): array {
            $a['PRODUCTS_SHIPPING_LINK'] = trim(str_replace(["\r\n", "\r", "\n"], "", $a['PRODUCTS_SHIPPING_LINK']));
            $a['PRODUCTS_SHIPPING_LINK'] = preg_replace('#\s{2,}#', ' ', $a['PRODUCTS_SHIPPING_LINK']);
            
            return $a;
        };
        
        $dataSorted     = array_map($removeShippingLinkLineBreaks, $dataSorted);
        $originalSorted = array_map($removeShippingLinkLineBreaks, $originalSorted);
        
        /**
         * None existing vpe ids can contain an empty string and in edge cases a null value
         * this callback normalises it to empty string
         */
        $normalizeVpeIds = static function ($a): array {
            $a['PRODUCTS_VPE'] = $a['PRODUCTS_VPE'] ?? '';
            
            return $a;
        };
        
        $dataSorted     = array_map($normalizeVpeIds, $dataSorted);
        $originalSorted = array_map($normalizeVpeIds, $originalSorted);
        
        /**
         * After a new image is added to a product the legacy implementation
         * will return the IMAGE_H and IMAGE_W as integer afterwards always as string.
         */
        $normalizeImageSizes = function (array $a): array {
            $a['PRODUCTS_IMAGE_W'] = (int)$a['PRODUCTS_IMAGE_W'];
            $a['PRODUCTS_IMAGE_H'] = (int)$a['PRODUCTS_IMAGE_H'];
            
            return $a;
        };
        
        $dataSorted     = array_map($normalizeImageSizes, $dataSorted);
        $originalSorted = array_map($normalizeImageSizes, $originalSorted);
        
        /**
         * Removing key "expires_date", since it does not reliably
         * provide the date in legacy application
         */
        $callback       = static function ($arr): array {
            if (isset($arr['PRODUCTS_EXPIRES'])) {
                unset($arr['PRODUCTS_EXPIRES']);
            }
            
            return $arr;
        };
        $dataSorted     = array_map($callback, $dataSorted);
        $originalSorted = array_map($callback, $originalSorted);
        
        return [$dataSorted, $originalSorted];
    }
    
    
    /**
     * @inheritDoc
     * @throws Exception
     */
    public function getAlsoPurchased(array $original, int $productId): array
    {
        $filter = $this->filtersFactory->createAlsoPurchased($productId, $this->control->settings());
        
        return $this->getListing($original, $filter);
    }
    
    
    /**
     * @inheritDoc
     * @throws Exception
     */
    public function getLastViewed(array $original, int $productId): array
    {
        $filter = $this->filtersFactory->createLastViewed($productId, $this->control->settings());
        
        return $this->getListing($original, $filter);
    }
    
    
    /**
     * @inheritDoc
     * @throws Exception
     */
    public function getNewProducts(array $original): array
    {
        $filter = $this->filtersFactory->createNewProducts($this->control->settings());
        
        return $this->getListing($original, $filter);
    }
    
    
    /**
     * @inheritDoc
     * @throws Exception
     */
    public function getSpecialsBox(array $original): array
    {
        $filter = $this->filtersFactory->createSpecialsBox($this->control->settings());
        
        return $this->getListing($original, $filter);
    }
    
    
    /**
     * @inheritDoc
     * @throws Exception
     */
    public function getSpecials(array $original): array
    {
        $filter = $this->filtersFactory->createSpecials($this->control->settings());
        
        return $this->getListing($original, $filter);
    }
    
    
    /**
     * @inheritDoc
     * @throws Exception
     */
    public function getStartpageSpecials(array $original): array
    {
        $filter = $this->filtersFactory->createStartpageSpecials($this->control->settings());
        
        return $this->getListing($original, $filter);
    }
    
    
    /**
     * @inheritDoc
     * @throws Exception
     */
    public function getTopProducts(array $original): array
    {
        $filter = $this->filtersFactory->createTopProducts($this->control->settings());
        
        return $this->getListing($original, $filter);
    }
    
    
    /**
     * @inheritDoc
     * @throws Exception
     */
    public function getUpcomingProducts(array $original): array
    {
        $filter = $this->filtersFactory->createUpcomingProducts($this->control->settings());
        
        return $this->getListing($original, $filter);
    }
    
    
    /**
     * @inheritDoc
     * @throws Exception
     */
    public function getWhatIsNewBoxProducts(array $original, ?int $excludedProductId = null): array
    {
        $filter = $this->filtersFactory->createWhatIsNewBoxProducts($this->control->settings(), $excludedProductId);
        
        return $this->getListing($original, $filter);
    }
    
    
    /**
     * @inheritDoc
     * @throws Exception
     */
    public function getCrossSellingProducts(array $original, int $baseProductId, bool $isReversed): array
    {
        $filter = $this->filtersFactory->createCrossSellingProducts($this->control->settings(),
                                                                    $baseProductId,
                                                                    $isReversed);
        
        return $this->getListing($original, $filter);
    }
}