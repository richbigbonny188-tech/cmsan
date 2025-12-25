<?php
/*--------------------------------------------------------------
   AddANewImageToAnImageListAction.php 2022-08-30
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Api\Modules\ImageList\App\Actions;

use Gambio\Admin\Modules\ImageList\Model\ValueObjects\ImageAltTitle;
use Gambio\Admin\Modules\ImageList\Model\ValueObjects\ImageTitle;
use Gambio\Admin\Modules\ImageList\Services\Exceptions\ImageListDoesNotExistException;
use Gambio\Admin\Modules\ImageList\Services\ImageListFactory;
use Gambio\Admin\Modules\ImageList\Services\ImageListReadService;
use Gambio\Admin\Modules\ImageList\Services\ImageListWriteService;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;
use Gambio\Core\Language\Services\LanguageService;
use Throwable;

/**
 * Class AddANewImageToAnImageListAction
 * @package Gambio\Api\Modules\ImageList\App\Actions
 */
class AddANewImageToAnImageListAction
{
    /**
     * @var ImageListFactory
     */
    private $factory;
    
    /**
     * @var ImageListReadService
     */
    private $readService;
    
    /**
     * @var ImageListWriteService
     */
    private $writeService;
    
    /**
     * @var LanguageService
     */
    private $languageService;
    
    
    /**
     * AddANewImageToAnImageListAction constructor.
     *
     * @param ImageListFactory      $factory
     * @param ImageListReadService  $readService
     * @param ImageListWriteService $writeService
     * @param LanguageService       $languageService
     */
    public function __construct(
        ImageListFactory      $factory,
        ImageListReadService  $readService,
        ImageListWriteService $writeService,
        LanguageService       $languageService
    ) {
        $this->factory         = $factory;
        $this->readService     = $readService;
        $this->writeService    = $writeService;
        $this->languageService = $languageService;
    }
    
    
    /**
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     *
     * @return Response
     */
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $relativePath = $request->getAttribute('relativePath');
        $absolutePath = dirname(__DIR__, 5) . '/images/product_images/original_images/' . $relativePath;
        
        if (($imageListId = (int)$request->getAttribute('imageListId')) === 0) {
            return $response->withStatus(409)->withJson(['errors' => [['imageListId can\'t be 0']]]);
        }
        
        if (file_exists(dirname($absolutePath)) === false) {
            
            mkdir(dirname($absolutePath), 0777, true);
        }
        
        if (file_exists($absolutePath)) {
            return $response->withStatus(409)->withJson([
                                                            'errors' => [
                                                                [
                                                                    'Image with relative path "' . $relativePath
                                                                    . '" does already exist.'
                                                                ]
                                                            ]
                                                        ]);
        }
        
        try {
            if (@file_put_contents($absolutePath, $request->getBody()->getContents()) === false) {
                return $response->withStatus(500)->withJson([
                                                                'errors' => [
                                                                    [
                                                                        'Could not write file "' . $absolutePath . '".'
                                                                    ]
                                                                ]
                                                            ]);
            }
            
            $imageList = $this->readService->getImageListById($imageListId);
            
            $newImage = $this->factory->createNewImage(
                $this->factory->createImagePath($relativePath),
                $this->factory->createImageTitles(...$this->createDefaultImageTitles()),
                $this->factory->createImageAltTitles(...$this->createDefaultImageAltTitles()),
                $imageList->images()->getHighestSortValue() + 1
            );
            
            $imageList->addNewImages($newImage);
            $this->writeService->storeImageLists($imageList);
            
            return $response->withStatus(201)->withJson(['data' => [$newImage->relativePath()]]);
        } catch (ImageListDoesNotExistException $exception) {
            @unlink($absolutePath);
            
            return $response->withStatus(404)->withJson(['error' => [$exception->getMessage()]]);
        } catch (Throwable $exception) {
            @unlink($absolutePath);
            
            throw $exception;
        }
    }
    
    
    /**
     * Creates the default image titles for all available languages
     * 
     * @return ImageTitle[]
     */
    private function createDefaultImageTitles(): array
    {
        $defaultTexts = ['de' => 'Artikelbild', 'en' => 'Product image'];
        $result       = [];
        
        foreach ($this->languageService->getAvailableLanguages()->toArray() as ['code' => $code]) {
        
            $result[] = $this->factory->createImageTitle($code, $defaultTexts[$code] ?? $defaultTexts['en']);
        }
        
        return $result;
    }
    
    
    /**
     * Creates the default image alt titles for all available languages
     *
     * @return ImageAltTitle[]
     */
    private function createDefaultImageAltTitles(): array
    {
        $defaultTexts = ['de' => 'Artikelbild', 'en' => 'Product image'];
        $result       = [];
    
        foreach ($this->languageService->getAvailableLanguages()->toArray() as ['code' => $code]) {
        
            $result[] = $this->factory->createImageAltTitle($code, $defaultTexts[$code] ?? $defaultTexts['en']);
        }
    
        return $result;
    }
}