<?php
/*--------------------------------------------------------------
   FetchAllCustomerHistoryEntriesAction.php 2022-08-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\History\App\Actions\JSON;

use DateTimeInterface;
use Gambio\Admin\Modules\Customer\App\CustomerProductRepository;
use Gambio\Admin\Modules\Customer\Submodules\History\Services\CustomerHistoryReadService as CustomerHistoryReadServiceInterface;
use Gambio\Core\Application\Http\AbstractAction;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;
use Gambio\Core\Application\ValueObjects\UserPreferences;

/**
 * Class FetchAllCustomerHistoryEntriesAction
 *
 * @package Gambio\Admin\Modules\Customer\Submodules\History\App\Actions
 * @codeCoverageIgnore
 */
class FetchAllCustomerHistoryEntriesAction extends AbstractAction
{
    private CustomerHistoryReadServiceInterface $service;
    private CustomerProductRepository           $productRepository;
    private int                                 $languageId;
    private string                              $datetimeFormat;
    
    
    /**
     * @param CustomerHistoryReadServiceInterface $historyReadService
     * @param CustomerProductRepository           $productRepository
     * @param UserPreferences                     $userPreferences
     */
    public function __construct(
        CustomerHistoryReadServiceInterface $historyReadService,
        CustomerProductRepository           $productRepository,
        UserPreferences                     $userPreferences
    ) {
        $this->service           = $historyReadService;
        $this->productRepository = $productRepository;
        $this->languageId        = $userPreferences->languageId();
        $this->datetimeFormat    = DateTimeInterface::ATOM;
    }
    
    
    /**
     * @inheritDoc
     */
    public function handle(Request $request, Response $response): Response
    {
        $customerId   = (int)$request->getAttribute('customerId');
        $responseData = ['history' => $this->service->getCustomerHistory($customerId)->toArray($this->datetimeFormat)];
    
        $productIds = [];
        foreach ($responseData['history'] as $entry) {
    
            if (isset($entry['payload']['products_id'])) {
        
                $productIds[] = (int)$entry['payload']['products_id'];
            }
        }
    
        $responseData['products'] = $this->productRepository->getProductsNameAndImage($this->languageId, ...$productIds)->toArray();
        
        return $response->withJson(['data' => $responseData]);
    }
}