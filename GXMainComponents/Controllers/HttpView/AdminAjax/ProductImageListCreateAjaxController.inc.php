<?php
/* --------------------------------------------------------------
  ProductImageListCreateAjaxController.inc.php 2020-02-04
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2020 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

use Gambio\ProductImageList\CreateService\Dtos\ImageListImageDto;
use Gambio\ProductImageList\Image\Collections\TextCollection;
use Gambio\ProductImageList\Image\Exceptions\FileDoesNotExistException;
use Gambio\ProductImageList\Image\ValueObjects\AltTitle;
use Gambio\ProductImageList\Image\ValueObjects\Id;
use Gambio\ProductImageList\Image\ValueObjects\LocalFilePath;
use Gambio\ProductImageList\Image\ValueObjects\Title;
use Gambio\ProductImageList\ImageList\ValueObjects\ListId;
use Gambio\ProductImageList\Interfaces\ProductImageListCreateServiceInterface;
use Gambio\ProductImageList\Image\ValueObjects\LanguageCode;

/**
 * Class ProductImageListCreateAjaxController
 */
class ProductImageListCreateAjaxController extends AdminHttpViewController
{
    /**
     * @var ProductImageListCreateServiceInterface
     */
    protected $createService;
    
    /**
     * @var LanguageTextManager
     */
    protected $languageTextManager;
    
    
    /**
     * @return JsonHttpControllerResponse
     */
    public function actionImageList(): JsonHttpControllerResponse
    {
        $response = [];
        $success  = true;
        $listName = $this->_getPostData('listName');
        
        if ($listName === null) {
            
            $success = false;
            $response['message'] = $this->languageTextManager()->get_text('CONTROLLER_MESSAGE_MISSING_LIST_NAME');
        } else {
            
            $this->createService()->createImageList($listName);
            $response['message'] = $this->languageTextManager()->get_text('CONTROLLER_MESSAGE_LIST_ADDED');
        }
        
        $response['success'] = $success;
    
        return new JsonHttpControllerResponse($response);
    }
    
    
    /**
     * PostData:
     * listId: string|int
     * localPath: string
     * titles: (JSON)[{
     *    value: string
     *    languageCode: string
     * }}
     *
     * altTitles: (JSON)[{
     *    value: string
     *    languageCode: string
     * }]
     *
     * @return JsonHttpControllerResponse
     * @throws FileDoesNotExistException
     */
    public function actionImage(): JsonHttpControllerResponse
    {
        $listId    = $this->_getPostData('listId');
        $localPath = $this->_getPostData('localPath');
        $titles    = $this->_getPostData('titles');
        $altTitles = $this->_getPostData('altTitles');
    
        if (empty($listId) || empty($titles) || empty($localPath) || empty($altTitles)) {
    
            return new JsonHttpControllerResponse(['success' => false]);
        }
        
        $titles    = json_decode(stripslashes($titles), true);
        $altTitles = json_decode(stripslashes($altTitles), true);
    
        $imageDto = new ImageListImageDto((int) $listId, $localPath);
        $imageId   = $this->createService()->createImage($imageDto);
        $titles    = $this->createTextCollection($imageId, Title::class, $titles);
        $altTitles = $this->createTextCollection($imageId, AltTitle::class, $altTitles);
        
        $this->createService()->createImageTexts($titles, $altTitles);
        
        return new JsonHttpControllerResponse([
            'success' => true,
            'message' => $this->languageTextManager()->get_text('CONTROLLER_MESSAGE_IMAGE_ADDED')
        ]);
    }
    
    /**
     * @return ProductImageListCreateServiceInterface
     */
    protected function createService(): ProductImageListCreateServiceInterface
    {
        if($this->createService === null) {
            
            $this->createService = StaticGXCoreLoader::getService('ProductImageListCreate');
        }
        
        return $this->createService;
    }
    
    
    /**
     * @return LanguageTextManager
     */
    protected function languageTextManager(): LanguageTextManager
    {
        if ($this->languageTextManager === null) {
            
            $this->languageTextManager = new LanguageTextManager('product_image_lists');
        }
        
        return $this->languageTextManager;
    }
    
    
    /**
     * @param int $listId
     *
     * @return ListId
     */
    protected function createListId(int $listId): ListId
    {
        return new ListId($listId);
    }
    
    
    /**
     * @param string $path
     *
     * @return LocalFilePath
     * @throws FileDoesNotExistException
     */
    protected function createLocalFilePath(string $path): LocalFilePath
    {
        return new LocalFilePath($path);
    }
    
    
    /**
     * @param Id     $imageId
     * @param string $textClass
     * @param array  $data
     *
     * @return TextCollection
     */
    protected function createTextCollection(Id $imageId, string $textClass, array $data): TextCollection
    {
        $textCollection = new TextCollection;
        
        if (count($data)) {
            
            foreach ($data as ['value' => $value, 'languageCode' => $languageCode]) {
                
                $textCollection[] = new $textClass($imageId, $value, new LanguageCode($languageCode));
            }
        }
        
        return $textCollection;
    }
}