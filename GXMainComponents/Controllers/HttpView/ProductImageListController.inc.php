<?php
/* --------------------------------------------------------------
  ProductImageListController.inc.php 2020-01-22
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2020 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

use Gambio\ProductImageList\Interfaces\ProductImageListReadServiceInterface;
use Gambio\ProductImageList\ImageList\ValueObjects\ListId;
use Gambio\ProductImageList\Exceptions\ImageListDoesNotExistsException;

/**
 * Class ProductImageListController
 */
class ProductImageListController extends AdminHttpViewController
{
    /**
     * @var LanguageTextManager
     */
    protected $languageTextManager;
    
    /**
     * @var ProductImageListReadServiceInterface
     */
    protected $productImageListReadService;
    
    
    /**
     * ProductImageListController constructor.
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
    
        $this->languageTextManager         = new LanguageTextManager('admin_product_image_list');
        $this->productImageListReadService = StaticGXCoreLoader::getService('ProductImageListRead');
    }
    
    
    /**
     * Returns the Template Configuration Page
     *
     * @return HttpControllerResponse|RedirectHttpControllerResponse
     */
    public function actionDefault()
    {
        $title    = new NonEmptyStringType($this->languageTextManager->get_text('HEADING_TITLE'));
        $template = new ExistingFile(new NonEmptyStringType(DIR_FS_ADMIN . '/html/content/product_image_list.html'));
        $data     = new KeyValueCollection([
                                               'ImageListsCollection' => $this->productImageListReadService->getImageLists()
                                           ]);

        return MainFactory::create('AdminLayoutHttpControllerResponse', $title, $template, $data);
    }

    /**
     * @return bool|mixed
     *
     * @throws ImageListDoesNotExistsException
     */
    public function actionEdit()
    {
        $listId = $this->queryParametersArray['listId'];
        $title = new NonEmptyStringType($this->languageTextManager->get_text('HEADING_TITLE'));
        $template = new ExistingFile(
            new NonEmptyStringType(DIR_FS_ADMIN . '/html/content/edit_product_image_list.html')
        );

        $imageList = $this->productImageListReadService
            ->getImageLists()
            ->getImageListById(
                new ListId($listId)
            );
        $data = new KeyValueCollection(
            ['ImagesListCollection' => $imageList]
        );

        return MainFactory::create('AdminLayoutHttpControllerResponse', $title, $template, $data);
    }
}