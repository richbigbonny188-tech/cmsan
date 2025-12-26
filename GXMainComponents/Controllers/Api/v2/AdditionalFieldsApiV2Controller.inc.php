<?php
/*--------------------------------------------------------------
   AdditionalFieldsApiV2Controller.inc.php 2023-03-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

use Gambio\Api\Application\Responses\ResponseDataTrimmerTrait;
use Gambio\MainComponents\Services\Core\AdditionalFields\Services\AdditionalFieldFactory;
use Gambio\MainComponents\Services\Core\AdditionalFields\Services\AdditionalFieldsReadService as AdditionalFieldsReadServiceInterface;
use Gambio\MainComponents\Services\Core\AdditionalFields\Services\AdditionalFieldsWriteService as AdditionalFieldsWriteServiceInterface;

/**
 * Class AdditionalFieldsApiV2Controller
 */
class AdditionalFieldsApiV2Controller extends HttpApiV2Controller
{
    use ResponseDataTrimmerTrait;
    
    /**
     * @var AdditionalFieldsReadServiceInterface
     */
    protected $readService;
    
    /**
     * @var AdditionalFieldsWriteServiceInterface
     */
    protected $writeService;
    
    /**
     * @var AdditionalFieldFactory
     */
    protected $factory;
    
    
    /**
     * Initialize controller components.
     */
    protected function init()
    {
        $this->readService  = StaticGXCoreLoader::getService('AdditionalFieldsRead');
        $this->writeService = StaticGXCoreLoader::getService('AdditionalFieldsWrite');
        $this->factory      = StaticGXCoreLoader::getService('AdditionalFieldsFactory');
    }
    
    
    /**
     * @throws HttpApiV2Exception
     */
    public function get(): void
    {
        if ($this->uri === ['additional_fields']) {
            
            $this->getAllAdditionalFields();
        }
        
        if (count($this->uri) === 2) {
            
            $this->getSpecifiedAdditionalField();
        }
    }
    
    /**
     * @throws HttpApiV2Exception
     */
    public function post(): void
    {
        if ($this->uri === ['additional_fields']) {
            
            $this->createAdditionalField();
        }
    }
    
    
    /**
     * @throws HttpApiV2Exception
     */
    public function delete(): void
    {
        if (count($this->uri) === 2) {
            
            $this->deleteAdditionalFields();
        }
    }
    
    
    /**
     * @throws HttpApiV2Exception
     */
    public function put(): void
    {
        if (count($this->uri) === 2) {
            
            $this->updateAdditionalField();
        }
    }
    
    /**
     * @throws HttpApiV2Exception
     */
    protected function deleteAdditionalFields(): void
    {
        try {
        
            $id = (int)$this->uri[1];
        
            $this->writeService->deleteAdditionalFields($id);
    
            $response = [
                'code'              => 200,
                'status'            => 'success',
                'action'            => 'delete',
                'additionalFieldId' => $id,
            ];
        
            $this->_writeResponse($response);
        } catch (Exception $exception) {
        
            throw new HttpApiV2Exception($exception->getMessage(), 400);
        }
    }
    
    /**
     * @throws HttpApiV2Exception
     */
    protected function createAdditionalField(): void
    {
        try {
        
            $response           = [];
            $parsedBody         = $this->request->getParsedBody();
            $additionalFieldIds = $this->writeService->createAdditionalField($parsedBody);
        
            foreach ($additionalFieldIds as $additionalFieldId) {
            
                $response[] = $this->readService->getAdditionalFieldById($additionalFieldId->value())->toArray();
            }
        
            $this->_writeResponse($response);
        } catch (Exception $exception) {
        
            throw new HttpApiV2Exception($exception->getMessage(), 400);
        }
    }
    
    protected function getAllAdditionalFields(): void
    {
        $searchTerm = $this->request->getQueryParam('search') ?? $this->request->getQueryParam('q');
        $page       = $this->request->getQueryParam('page') ?? 0;
        $perPage    = $this->request->getQueryParam('per_page') ?? 50;
        $fields     = $this->request->getQueryParam('fields');
        $sort       = $this->request->getQueryParam('sort');
    
        $page    = is_numeric($page) ? (int)$page : 0;
        $perPage = is_numeric($perPage) ? (int)$perPage : 50;
        $fields  = $fields ? explode(',', $fields) : null;
        $sort    = is_string($sort) ? ($sort === '' ? null : trim($sort)) : $sort;
    
        $additionalFields = $this->readService->getAllAdditionalFields($page, $perPage, $searchTerm, $sort);
        $response         = $additionalFields->toArray();
        
        if ($fields !== null) {
        
            $response = $this->trimCollectionData($response, $fields);
        }
        
        $this->_writeResponse($response);
    }
    
    
    /**
     * @throws HttpApiV2Exception
     */
    protected function getSpecifiedAdditionalField(): void
    {
        try {
    
            $id              = (int)$this->uri[1];
            $additionalField = $this->readService->getAdditionalFieldById($id);
    
            $this->_writeResponse($additionalField->toArray());
            
        } catch (Exception $exception) {
    
            throw new HttpApiV2Exception($exception->getMessage(), 404);
        }
    }
    
    
    /**
     * @throws HttpApiV2Exception
     */
    protected function updateAdditionalField(): void
    {
        try {
            $id         = (int)$this->uri[1];
            $parsedBody = $this->request->getParsedBody();
            
            if ((isset($parsedBody['names']) && is_array($parsedBody['names'])) === false) {
                
                throw new Exception('Invalid names array supplied');
            }
            
            $additionalField = $this->readService->getAdditionalFieldById($id);
    
            foreach ($parsedBody['names'] as $languageCode => $name) {
                
                $updatedFieldName = $this->factory->createFieldName(strtolower($languageCode), $name);
                
                $additionalField->changeFieldName($updatedFieldName);
            }
            
            $this->writeService->storeAdditionalFields($additionalField);
            
        } catch (Exception $exception) {
    
            throw new HttpApiV2Exception($exception->getMessage(), 400);
        }
    }
}