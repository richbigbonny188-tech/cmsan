<?php
/* --------------------------------------------------------------
  ProductImageListReadAjaxController.inc.php 2020-02-04
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2020 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

use Gambio\ProductImageList\Interfaces\ProductImageListReadServiceInterface;
use Gambio\ProductImageList\ReadService\Dtos\CombiModelAndProductsIdDto;
use Gambio\ProductImageList\ReadService\Exceptions\CombinationDoesNotHaveAListException;

/**
 * Class ProductImageListReadAjaxController
 */
class ProductImageListReadAjaxController extends AdminHttpViewController
{
    /**
     * @var ProductImageListReadServiceInterface
     */
    protected $readService;
    
    
    /**
     * Used for getting all the available lists
     *
     * @return JsonHttpControllerResponse
     */
    public function actionDefault(): JsonHttpControllerResponse
    {
        $result = [];
        $lists  = $this->readService()->getImageLists();
        
        if (count($lists)) {
    
            foreach ($lists as $list) {
                $result[] = [
                    "id"   => $list->listId()->value(),
                    "name" => $list->listName()->value(),
                ];
            }
        }
    
        return new JsonHttpControllerResponse($result);
    }
    
    
    /**
     * @return JsonHttpControllerResponse
     */
    public function actionList(): JsonHttpControllerResponse
    {
        $listId = (int)$this->_getQueryParameter('id');
        
        if ($listId === 0) {
            
            throw new InvalidArgumentException('No Id specified');
        }
    
        $list = $this->readService()->getImageListById($listId);
        
        return new JsonHttpControllerResponse([$list]);
    }
    
    
    /**
     * @return JsonHttpControllerResponse
     */
    public function actionListByProductsIdAndCombiModel(): JsonHttpControllerResponse
    {
        $productsId = (int)$this->_getQueryParameter('productsId');
        $combiModel = (string)$this->_getQueryParameter('combiModel');
        
        if ($productsId === 0 || $combiModel === null) {
            
            throw new InvalidArgumentException('"productsId" and "combiModel" need to be supplied');
        }
        
        $dto = new CombiModelAndProductsIdDto($combiModel, $productsId);
        
        try {
            $list = $this->readService()->getImageListByCombiModelAndProductsId($dto);
        } catch (CombinationDoesNotHaveAListException $exception) {
            return new JsonHttpControllerResponse(['success' => false]);
        }
        
        return new JsonHttpControllerResponse([$list]);
    }
    
    /**
     * @return ProductImageListReadServiceInterface
     */
    protected function readService(): ProductImageListReadServiceInterface
    {
    	if($this->readService === null) {
    	
    		$this->readService = StaticGXCoreLoader::getService('ProductImageListRead');
    	}
    	
    	return $this->readService;
    }
}