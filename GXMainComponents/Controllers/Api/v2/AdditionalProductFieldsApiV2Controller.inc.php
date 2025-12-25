<?php
/*--------------------------------------------------------------
   AdditionalProductFieldsApiV2Controller.inc.php 2023-03-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

use Gambio\MainComponents\Services\Core\AdditionalProductFields\Model\AdditionalProductField;
use Gambio\MainComponents\Services\Core\AdditionalProductFields\Services\AdditionalProductFieldFactory;
use Gambio\MainComponents\Services\Core\AdditionalProductFields\Services\AdditionalProductFieldReadService as AdditionalProductFieldReadServiceInterface;
use Gambio\MainComponents\Services\Core\AdditionalProductFields\Services\AdditionalProductFieldWriteService as AdditionalProductFieldWriteServiceInterface;

/**
 * Class AdditionalProductFieldsApiV2Controller
 */
class AdditionalProductFieldsApiV2Controller extends HttpApiV2Controller
{
    /**
     * @var AdditionalProductFieldReadServiceInterface
     */
    protected $readService;
    
    /**
     * @var AdditionalProductFieldWriteServiceInterface
     */
    protected $writeService;
    
    /**
     * @var AdditionalProductFieldFactory
     */
    protected $factory;
    
    
    protected function init()
    {
        $this->readService  = StaticGXCoreLoader::getService('AdditionalProductFieldRead');
        $this->writeService = StaticGXCoreLoader::getService('AdditionalProductFieldWrite');
        $this->factory      = StaticGXCoreLoader::getService('AdditionalProductFieldFactory');
    }
    
    
    /**
     * @throws HttpApiV2Exception
     */
    public function post(): void
    {
        try {
        
            $productId  = (int)$this->uri[1];
            $parsedBody = $this->request->getParsedBody();
            $fieldIds   = [];
        
            foreach ($parsedBody as ['id' => $id, 'values' => $localizations]) {
            
                if (in_array(null, [$id, $localizations])) {
                
                    throw new Exception('Invalid creation arguments supplied');
                }
            
                $fieldIds[] = (int)$id;
                $values     = [];
            
                foreach ($localizations as $languageCode => $value) {
                
                    $values[strtolower($languageCode)] = $value;
                }
            
                $this->writeService->createAdditionalProductFields($productId, (int)$id, $values);
            }
        
            $response = $this->readService->getAdditionalProductFields($productId)->toArray();
            $response = $this->trimCreationResponse($response, ...$fieldIds);
        
            $this->_writeResponse($response);
        
        } catch (Exception $exception) {
        
            throw new HttpApiV2Exception($exception->getMessage(), 400);
        }
    }
    
    
    public function get(): void
    {
        $productId = (int)$this->uri[1];
        $response  = $this->readService->getAdditionalProductFields($productId)->toArray();
    
        $this->_writeResponse($response);
    }
    
    
    /**
     * @throws HttpApiV2Exception
     */
    public function put(): void
    {
        try {
            
            $productId        = (int)$this->uri[1];
            $parsedBody       = $this->request->getParsedBody();
            $additionalFields = $this->readService->getAdditionalProductFields($productId);
            $updatedFields    = [];
        
            foreach ($parsedBody as ['id' => $id, 'values' => $localizations]) {
    
                $fieldId         = $this->factory->createAdditionalFieldId((int)$id);
                $updatedFields[] = $additionalField = $additionalFields->getByFieldId($fieldId);
    
                foreach ($localizations as $languageCode => $value) {
                    
                    $changedValue = $this->factory->createAdditionalFieldValue(strtolower($languageCode), $value);
                    $additionalField->changeValues($changedValue);
                }
            }
            
            $this->writeService->storeAdditionalProductField(...$updatedFields);
    
            $response = array_map(static function (AdditionalProductField $productField): array {
        
                return $productField->toArray();
            },
                $updatedFields);
    
            $this->_writeResponse($response);
            
        } catch (Exception $exception) {
    
            throw new HttpApiV2Exception($exception->getMessage(), 400);
        }
    }
    
    
    /**
     * @throws HttpApiV2Exception
     */
    public function delete(): void
    {
        try {
    
            $productId = (int)$this->uri[1];
            $fieldId   = (int)$this->uri[3];
            
            $this->writeService->deleteAdditionalProductField($productId, $fieldId);
    
            $response = [
                'code'              => 200,
                'status'            => 'success',
                'action'            => 'delete',
                'additionalFieldId' => $fieldId,
                'productId'         => $productId,
            ];
    
            $this->_writeResponse($response);
            
        } catch (Exception $exception) {
        
            throw new HttpApiV2Exception($exception->getMessage(), 400);
        }
    }
    
    /**
     * @param array $response
     * @param int   ...$fieldIds
     *
     * @return array
     */
    protected function trimCreationResponse(array $response, int ...$fieldIds): array
    {
        $filteredResponse = array_filter($response,
            static function (array $additionalProductField) use ($fieldIds): bool {
        
                return in_array($additionalProductField['id'], $fieldIds);
            });
        
        return array_values($filteredResponse);
    }
}