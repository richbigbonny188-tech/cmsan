<?php
/* --------------------------------------------------------------
  CookieConsentPanelVendorListAjaxController.php 2019-12-19
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

use Gambio\CookieConsentPanel\Services\Purposes\Factories\PurposeReaderServiceFactory;
use Gambio\CookieConsentPanel\Services\Purposes\Interfaces\PurposeReaderServiceFactoryInterface;
use Gambio\CookieConsentPanel\Services\Purposes\Interfaces\PurposeReaderServiceInterface;
use Gambio\CookieConsentPanel\Storage\CookieConsentPanelStorage;

/**
 * Class CookieConsentPanelVendorListAjaxController
 */
class CookieConsentPanelVendorListAjaxController extends HttpViewController
{
    /**
     * @var CookieConsentPanelFactoryInterface
     */
    protected $factory;
    /**
     * @var CookieConsentPanelStorage
     */
    protected $storage;
    
    /**
     * @var GoogleConfigurationStorage
     */
    protected $googleConfigurationStorage;
    /**
     * @var PurposeReaderServiceInterface
     */
    protected $purposeReaderService;
    /**
     * @var PurposeReaderServiceFactoryInterface
     */
    protected $purposeReaderServiceFactory;
    
    
    /**
     * CookieConsentPanelVendorListAjaxController constructor.
     *
     * @param HttpContextReaderInterface     $httpContextReader
     * @param HttpResponseProcessorInterface $httpResponseProcessor
     * @param ContentViewInterface           $defaultContentView
     */
    public function __construct(
        HttpContextReaderInterface $httpContextReader,
        HttpResponseProcessorInterface $httpResponseProcessor,
        ContentViewInterface $defaultContentView
    
    ) {
        parent::__construct($httpContextReader, $httpResponseProcessor, $defaultContentView);
        $this->purposeReaderService = $this->purposeReaderServiceFactory()->service();
    }
    
    
    /**
     * @return CookieConsentPanelFactory
     */
    protected function factory(): CookieConsentPanelFactoryInterface
    {
        if ($this->factory === null) {
            /**
             * Method Factory is being used here because the is no way to inject the factory from the extension
             */
            $this->factory = new CookieConsentPanelFactory();
        }
        
        return $this->factory;
    }
    
    
    /**
     * @return PurposeReaderServiceFactoryInterface
     */
    protected function purposeReaderServiceFactory(): PurposeReaderServiceFactoryInterface
    {
        if ($this->purposeReaderServiceFactory === null) {
            
            $this->purposeReaderServiceFactory = new PurposeReaderServiceFactory();
        }
        
        return $this->purposeReaderServiceFactory;
    }
    
    
    
    /**
     * @return JsonHttpControllerResponse
     */
    public function actionList(): JsonHttpControllerResponse
    {
        $manager = $this->factory()->createManager();
        $json    = [
            'vendorListVersion' => 2,
            'lastUpdated'       => "2019-12-12T16:00:28Z",
            'categories'        => $this->categories(),
            'purposes'          => $this->purposes(),
            'features'          => [],
            'vendors'           => $manager->vendorCookiesList(),
        ];
        
        return MainFactory::create('JsonHttpControllerResponse', $json);
    }
    
    
    /**
     * @param string $key
     *
     * @return string
     */
    protected function getStorageContent(string $key): string
    {
        $content = $this->storage()->get($key);
        $json    = json_decode($content, false);
        
        return $json->{$_SESSION['language_code']};
    }
    
    
    /**
     * @return CookieConsentPanelStorage
     */
    protected function storage(): CookieConsentPanelStorage
    {
        if ($this->storage === null) {
            
            $this->storage = new CookieConsentPanelStorage;
        }
        
        return $this->storage;
    }
    
    
    /**
     * @return array
     */
    protected function purposes(): array
    {
        return $this->purposeReaderService->activePurposes($_SESSION['languages_id']);
    }

    
    /**
     * @return array
     */
    protected function categories(): array
    {
        return $this->purposeReaderService->categories((int)$_SESSION['languages_id']);
    }
    
    
    /**
     * @return GoogleConfigurationStorage
     */
    protected function googleConfigurationStorage(): GoogleConfigurationStorage
    {
        if ($this->googleConfigurationStorage === null) {
    
            $this->googleConfigurationStorage = new GoogleConfigurationStorage(StaticGXCoreLoader::getDatabaseQueryBuilder(), 'analytics');
        }
        
        return $this->googleConfigurationStorage;
    }
}