<?php
/**
 * ProductImageListUpdateAjaxController.inc.php 2023-03-06
 * Last Modified: 2/4/20, 1:07 PM
 * Gambio GmbH
 * http://www.gambio.de
 * Copyright (c) 2023 Gambio GmbH
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
 */
declare(strict_types=1);

use Gambio\ProductImageList\Image\ValueObjects\Id;
use Gambio\ProductImageList\Image\ValueObjects\Title;
use Gambio\ProductImageList\Image\ValueObjects\AltTitle;
use Gambio\ProductImageList\Image\ValueObjects\LanguageCode;
use Gambio\ProductImageList\Image\Collections\TextCollection;
use Gambio\ProductImageList\Interfaces\ProductImageListUpdateServiceInterface;
use Gambio\ProductImageList\UpdateService\Dtos\UpdateImageListNameDto;
use Gambio\ProductImageList\UpdateService\Dtos\UpdateMultipleSortingDto;
use Gambio\ProductImageList\UpdateService\Dtos\UpdateSortingDto;

/**
 * Class ProductImageListUpdateAjaxController
 * Available Endpoints:
 * [POST] admin/admin.php?do=ProductImageListUpdateAjax/updateImagesSort
 * [POST] admin/admin.php?do=ProductImageListUpdateAjax/updateImageText
 * [POST] admin/admin.php?do=ProductImageListUpdateAjax/updateImageListName
 */
class ProductImageListUpdateAjaxController extends AdminHttpViewController
{
    /**
     * @var ProductImageListUpdateServiceInterface
     */
    protected $productImageListUpdateService;
    
    /**
     * @var LanguageTextManager
     */
    protected $languageTextManager;
    
    
    /**
     * Init
     */
    public function init()
    {
        $this->productImageListUpdateService = StaticGXCoreLoader::getService('ProductImageListUpdate');
        $this->languageTextManager           = MainFactory::create('LanguageTextManager', 'product_image_lists');
    }
    
    
    /**
     * PostData:
     * sort: (JSON)
     * @example
     *  [
     *      {
     *          "imageId": int,
     *          "sortIndex": int
     *      },
     *      {
     *          "imageId": int,
     *          "sortIndex": int
     *      }
     *  ]
     *
     * @return JsonHttpControllerResponse
     * @throws InvalidArgumentException
     */
    public function actionUpdateImagesSort() : JsonHttpControllerResponse
    {
        $success = false;
    
        try {
            $this->validateUpdateRequestMethod();
        
            $sortJson = $this->_getPostData('sort');
            if (!$sortJson) {
                throw new InvalidArgumentException(
                    $this->getTranslatedText('CONTROLLER_MESSAGE_MISSING_JSON_REQUIRED_INPUT') . ' "sort"'
                );
            }
        
            if (!$this->isValidJson($sortJson)) {
                throw new InvalidArgumentException(
                    $this->getTranslatedText('CONTROLLER_MESSAGE_INVALID_JSON_INPUT')
                );
            }
        
            $sortDtos = [];
            $sortData = $this->prepareJsonInput($sortJson);
            foreach ($sortData as $sort) {
                $sortDtos[] = new UpdateSortingDto($sort['imageId'], $sort['sortIndex']);
            }
        
            $multipleSortingDto = new UpdateMultipleSortingDto($sortDtos);
            $this->productImageListUpdateService->updateImagesSort($multipleSortingDto);
            $success = true;
            $message = $this->getTranslatedText('CONTROLLER_MESSAGE_IMAGES_SORTED');
        } catch (Throwable $e) {
            $message = $e->getMessage();
        }
    
        $response = [
            'success' => $success,
            'message' => $message,
        ];
    
        return new JsonHttpControllerResponse($response);
    }
    
    
    /**
     * PostData:
     * imageId: int
     * titles: (JSON)[{
     *    value: string
     *    languageCode: string
     * }]
     * altTitles: (JSON)[{
     *    value: string
     *    languageCode: string
     * }]
     *
     * @return JsonHttpControllerResponse
     * @throws InvalidArgumentException
     */
    public function actionUpdateImageText() : JsonHttpControllerResponse
    {
        $success = false;
    
        try {
            $this->validateUpdateRequestMethod();
        
            $imageIdJson       = (int)$this->_getPostData('imageId');
            $titlesJson    = $this->_getPostData('titles');
            $altTitlesJson = $this->_getPostData('altTitles');
        
            if (!$imageIdJson || !$titlesJson || !$altTitlesJson) {
                throw new InvalidArgumentException(
                    $this->getTranslatedText('CONTROLLER_MESSAGE_MISSING_JSON_REQUIRED_INPUT')
                );
            }
        
            if (!$this->isValidJson($titlesJson) || !$this->isValidJson($altTitlesJson)) {
                throw new InvalidArgumentException(
                    $this->getTranslatedText('CONTROLLER_MESSAGE_INVALID_JSON_INPUT')
                );
            }
        
            $imageId = new Id($imageIdJson);
            $titles    = $this->prepareJsonInput($titlesJson);
            $altTitles = $this->prepareJsonInput($altTitlesJson);
            
            $titlesTextCollection    = $this->createTextCollection($imageId, Title::class, $titles);
            $altTitlesTextCollection = $this->createTextCollection($imageId, AltTitle::class, $altTitles);
        
            $this->productImageListUpdateService->updateImageText($titlesTextCollection);
            $this->productImageListUpdateService->updateImageText($altTitlesTextCollection);
            $success = true;
            $message = $this->getTranslatedText('CONTROLLER_MESSAGE_IMAGE_TEXT_UPDATED');
        } catch (Throwable $e) {
            $message = $e->getMessage();
        }
    
        $response = [
            'success' => $success,
            'message' => $message,
        ];
    
        return new JsonHttpControllerResponse($response);
    }
    
    
    /**
     * PostData:
     * listId: int
     * listName: string
     *
     * @return JsonHttpControllerResponse
     * @throws InvalidArgumentException
     */
    public function actionUpdateImageListName() : JsonHttpControllerResponse
    {
        $success = false;
    
        try {
            $this->validateUpdateRequestMethod();
        
            $listId = (int)$this->_getPostData("listId");
            $listName    = (string)$this->_getPostData("listName");
        
            if (!$listId || !$listName) {
                throw new InvalidArgumentException(
                    $this->getTranslatedText('CONTROLLER_MESSAGE_MISSING_JSON_REQUIRED_INPUT')
                );
            }
        
            $listNameDto = new UpdateImageListNameDto($listId, $listName);
            $this->productImageListUpdateService->updateImageListName($listNameDto);
            $success = true;
            $message = $this->getTranslatedText('CONTROLLER_MESSAGE_LIST_NAME_UPDATED');
        } catch (Throwable $e) {
            $message = $e->getMessage();
        }
    
        $response = [
            'success' => $success,
            'message' => $message,
        ];
    
        return new JsonHttpControllerResponse($response);
    }
    
    
    /**
     * @param Id     $imageId
     * @param string $textClass
     * @param array  $data
     *
     * @return TextCollection
     */
    protected function createTextCollection(Id $imageId, string $textClass, array $data) : TextCollection
    {
        $textCollection = new TextCollection;
        if (count($data)) {
            foreach ($data as ['value' => $value, 'languageCode' => $languageCode]) {
                $textCollection[] = new $textClass($imageId, $value, new LanguageCode($languageCode));
            }
        }
        
        return $textCollection;
    }
    
    
    /**
     * @param string $phraseName
     *
     * @return string
     */
    protected function getTranslatedText(string $phraseName) : string
    {
        return $this->languageTextManager->get_text($phraseName);
    }
    
    
    /**
     * @throws Exception
     */
    protected function validateUpdateRequestMethod() : void
    {
        if (!$this->isValidRequestMethod('post')) {
            throw new Exception(
                $this->getTranslatedText('CONTROLLER_MESSAGE_INVALID_REQUEST_METHOD')
            );
        }
    }
    
}