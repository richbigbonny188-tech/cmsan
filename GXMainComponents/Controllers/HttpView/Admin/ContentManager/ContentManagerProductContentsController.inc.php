<?php
/* --------------------------------------------------------------
 ContentManagerProductContentsController.inc.php 2022-07-26
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2022 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Class ContentManagerProductContentsController
 *
 * @category System
 * @package  AdminHttpViewControllers
 */
class ContentManagerProductContentsController extends AdminHttpViewController
{
    use ContentManagerContentNavigationTrait;
    
    /**
     * @var \UserConfigurationService
     */
    protected $userConfigurationService;
    
    /**
     * @var \LanguageTextManager
     */
    protected $languageTextManager;
    
    /**
     * @var \LanguageProvider
     */
    protected $languageProvider;
    
    /**
     * @var \CI_DB_query_builder
     */
    protected $queryBuilder;
    
    /**
     * @var NonEmptyStringType
     */
    protected $title;
    
    /**
     * @var array
     */
    protected $descriptionFields = [
        'title',
        'content'
    ];
    
    /**
     * @var array
     */
    protected $resourceFields = [
        'file',
        'link',
        'text',
    ];
    
    
    /**
     * Initialize Controller
     */
    public function init()
    {
        $this->userConfigurationService = StaticGXCoreLoader::getService('UserConfiguration');
        $this->languageTextManager      = MainFactory::create('LanguageTextManager',
                                                              'content_manager',
                                                              $_SESSION['languages_id']);
        $this->queryBuilder             = StaticGXCoreLoader::getDatabaseQueryBuilder();
        $this->languageProvider         = MainFactory::create('LanguageProvider', $this->queryBuilder);
        $this->title                    = new NonEmptyStringType($this->languageTextManager->get_text('HEADING_TITLE'));
    }
    
    
    /**
     * Default actions, renders the content manager product contents overview.
     *
     * @return \AdminLayoutHttpControllerResponse
     */
    public function actionDefault()
    {
        $this->_setExpertMode();
        
        $template = new ExistingFile(new NonEmptyStringType(DIR_FS_ADMIN
                                                            . '/html/content/content_manager/product_contents/overview.html'));
        
        $data = MainFactory::create('KeyValueCollection',
                                    [
                                        'product_contents' => $this->_getOverviewData(),
                                    ]);
        
        return MainFactory::create('AdminLayoutHttpControllerResponse',
                                   $this->title,
                                   $template,
                                   $data,
                                   $this->_getAssets(),
                                   $this->_createContentNavigation($this->languageTextManager, 'productContents'));
    }
    
    
    public function actionEdit()
    {
        $this->_setExpertMode();
        
        $template = new ExistingFile(new NonEmptyStringType(DIR_FS_ADMIN
                                                            . '/html/content/content_manager/product_contents/edit.html'));
        
        $editData               = $this->_getEditData();
        $editData['groupCheck'] = GROUP_CHECK ? $this->_getProductContentCustomerGroups() : '';
        
        $data = MainFactory::create('KeyValueCollection',
                                    [
                                        'content_manager' => $editData,
                                        'contentType'     => $editData['type']
                                    ]);
        
        $contentData = $data->getValue('content_manager')[$data->getValue('contentType')];
        
        $title = new NonEmptyStringType($this->languageTextManager->get_text('CONTENT_TITLE') . ': '
                                        . $contentData['internal_name']);
        
        return MainFactory::create('AdminLayoutHttpControllerResponse',
                                   $title,
                                   $template,
                                   $data,
                                   $this->_getAssets(),
                                   $this->_createContentNavigation($this->languageTextManager, 'productContents'));
    }
    
    
    protected function _getProductContentCustomerGroups()
    {
        if (GROUP_CHECK === 'true') {
            $groupCheck     = [];
            $groupCheckData = $this->queryBuilder->select('customer_status_id')
                ->from('product_content_customer_status')
                ->where('product_content_id', $this->_getQueryParameter('id'))
                ->get()
                ->result_array();
            
            if (count($groupCheckData) === 0) {
                return '';
            }
            
            foreach ($groupCheckData as $customerStatusId) {
                $groupCheck[] = $customerStatusId['customer_status_id'];
            }
            
            return implode(',', $groupCheck);
        }
        
        return '';
    }
    
    
    /**
     * Saves product content file data.
     *
     * @return \RedirectHttpControllerResponse
     */
    public function actionSaveFile()
    {
    	$this->_verifyPageToken();
        $this->_setExpertMode();
        
        return $this->_insertProductContentPostData('file');
    }
    
    
    /**
     * Saves product content link data.
     *
     * @return \RedirectHttpControllerResponse
     */
    public function actionSaveLink()
    {
	    $this->_verifyPageToken();
	    $this->_setExpertMode();
        
        return $this->_insertProductContentPostData('link');
    }
    
    
    /**
     * Saves product content text data.
     *
     * @return \RedirectHttpControllerResponse
     */
    public function actionSaveText()
    {
	    $this->_verifyPageToken();
	    $this->_setExpertMode();
        
        return $this->_insertProductContentPostData('text');
    }
    
    
    /**
     * Return the form for creating a new product content.
     *
     * @return \AdminLayoutHttpControllerResponse|bool
     */
    public function actionNew()
    {
        $this->_setExpertMode();
        
        $template = new ExistingFile(new NonEmptyStringType(DIR_FS_ADMIN
                                                            . '/html/content/content_manager/product_contents/edit.html'));
        
        $ckIdentifier = [
            'file' => [],
            'link' => [],
            'text' => [],
        ];
        $ckTypes      = [
            'file' => [],
            'link' => [],
            'text' => [],
        ];
        foreach (['file', 'link', 'text'] as $type) {
            foreach ($this->languageProvider->getCodes()->getArray() as $languageCode) {
                $languageCode                       = $languageCode->asString();
                $ckIdentifier[$type][$languageCode] = 'content-manager-product-content-' . $type . '-new-content-'
                                                      . $languageCode;
                $ckTypes[$type][$languageCode]      = $this->userConfigurationService->getUserConfiguration(new IdType(0),
                                                                                                            $ckIdentifier[$type][$languageCode]) ? : 'ckeditor';
            }
        }
        
        $data = MainFactory::create('KeyValueCollection',
                                    [
                                        'contentType'     => $this->_getQueryParameter('type'),
                                        'content_manager' => [
                                            'page_token'            => $_SESSION['coo_page_token']->generate_token(),
                                            'filemanager_available' => $this->_isFilemanagerAvailable(),
                                            'filelist'              => $this->_getProductsContentFiles(),
                                            'link'                  => [
                                                'form_action' => 'admin.php?do=ContentManagerProductContents/saveLink',
                                                'ckeditor'    => [
                                                    'identifier' => $ckIdentifier['link'],
                                                    'type'       => $ckTypes['link'],
                                                ],
                                            ],
                                            'file'                  => [
                                                'form_action' => 'admin.php?do=ContentManagerProductContents/saveFile',
                                                'ckeditor'    => [
                                                    'identifier' => $ckIdentifier['file'],
                                                    'type'       => $ckTypes['file'],
                                                ],
                                            ],
                                            'text'                  => [
                                                'form_action' => 'admin.php?do=ContentManagerProductContents/saveText',
                                                'ckeditor'    => [
                                                    'identifier' => $ckIdentifier['text'],
                                                    'type'       => $ckTypes['text'],
                                                ],
                                            ],
                                            'groupCheck'            => 'all'
                                        ],
                                    ]);
        
        $title = new NonEmptyStringType($this->languageTextManager->get_text('NEW_CONTENT_TITLE'));
        
        return MainFactory::create('AdminLayoutHttpControllerResponse',
                                   $title,
                                   $template,
                                   $data,
                                   $this->_getAssets(),
                                   $this->_createContentNavigation($this->languageTextManager, 'productContents'));
    }
    
    
    /**
     * Returns the form to edit the product file content
     *
     * @return \AdminLayoutHttpControllerResponse|bool
     */
    public function actionEditFile()
    {
        $this->_setExpertMode();
        
        $template = new ExistingFile(new NonEmptyStringType(DIR_FS_ADMIN
                                                            . '/html/content/content_manager/product_contents/edit.html'));
        
        $data = MainFactory::create('KeyValueCollection',
                                    [
                                        'content_manager' => $this->_getEditData(),
                                        'contentType'     => 'file'
                                    ]);
        
        return MainFactory::create('AdminLayoutHttpControllerResponse',
                                   $this->title,
                                   $template,
                                   $data,
                                   $this->_getAssets(),
                                   $this->_createContentNavigation($this->languageTextManager, 'productContents'));
    }
    
    
    /**
     * Returns the form to edit the product link content
     *
     * @return \AdminLayoutHttpControllerResponse|bool
     */
    public function actionEditLink()
    {
        $this->_setExpertMode();
        
        $template = new ExistingFile(new NonEmptyStringType(DIR_FS_ADMIN
                                                            . '/html/content/content_manager/product_contents/edit.html'));
        
        $data = MainFactory::create('KeyValueCollection',
                                    [
                                        'content_manager' => $this->_getEditData(),
                                        'contentType'     => 'link'
                                    ]);
        
        return MainFactory::create('AdminLayoutHttpControllerResponse',
                                   $this->title,
                                   $template,
                                   $data,
                                   $this->_getAssets(),
                                   $this->_createContentNavigation($this->languageTextManager, 'productContents'));
    }
    
    
    public function actionUpdate()
    {
    	$this->_verifyPageToken();
        $this->_setExpertMode();
        
        $type             = $this->_getPostData('type');
        $productContentId = $this->_getQueryParameter('id');
        
        $productContentDescriptionData = $this->_updateIntoProductContentsAndReturnInsertId($type)
            ->_updateIntoProductContentProducts($productContentId, $type)
            ->_prepareProductContentDescriptionData($productContentId, $type);
        $productContentResourcesData   = $this->_prepareProductContentResourceData($type);
        
        if (GROUP_CHECK === 'true') {
            $this->queryBuilder->delete('product_content_customer_status', ['product_content_id' => $productContentId]);
            
            if ($this->_getPostData('content_manager')[$type]['group_check']) {
                foreach ($this->_getPostData('content_manager')[$type]['group_check'] as $customerStatusId) {
                    if ($customerStatusId !== 'all') {
                        $this->queryBuilder->insert('product_content_customer_status',
                                                    [
                                                        'product_content_id' => $productContentId,
                                                        'customer_status_id' => $customerStatusId
                                                    ]);
                    }
                }
            }
        }
        
        return $this->_updateIntoProductContentDescriptionsAndResources($productContentDescriptionData,
                                                                        $productContentResourcesData)
            ->_getUpdateResponse('ContentManagerProductContents', $productContentId, 'edit');
    }
    
    
    protected function _insertIntoProductContentsAndReturnInsertId($type)
    {
        $postData            = $this->_getPostData('content_manager')[$type];
        $productContentsData = [
            'name' => $postData['internal_name']
        ];
        
        $this->queryBuilder->insert('product_contents', $productContentsData);
        
        return $this->queryBuilder->insert_id();
    }
    
    
    protected function _updateIntoProductContentsAndReturnInsertId($type)
    {
        $postData            = $this->_getPostData('content_manager')[$type];
        $productContentsData = [
            'name' => $postData['internal_name']
        ];
        
        $this->queryBuilder->update('product_contents',
                                    $productContentsData,
                                    ['id' => $this->_getQueryParameter('id')]);
        
        return $this;
    }
    
    
    protected function _insertIntoProductContentProducts($productContentId, $type)
    {
        $postData        = $this->_getPostData('content_manager')[$type];
        $alreadyInserted = [];
        
        foreach ($postData['products'] as $productId) {
            if ($productId > 0 && !in_array($productId, $alreadyInserted)) {
                $this->queryBuilder->insert('product_content_products',
                                            [
                                                'product_content_id' => $productContentId,
                                                'product_id'         => $productId
                                            ]);
                $alreadyInserted[] = $productId;
            }
        }
        
        return $this;
    }
    
    
    protected function _updateIntoProductContentProducts($productContentId, $type)
    {
        // remove all attachments first
        $this->queryBuilder->delete('product_content_products',
                                    ['product_content_id' => $this->_getQueryParameter('id')]);
        $postData        = $this->_getPostData('content_manager')[$type];
        $alreadyInserted = [];
        
        foreach ($postData['products'] as $productId) {
            if ($productId > 0 && !in_array($productId, $alreadyInserted)) {
                $this->queryBuilder->insert('product_content_products',
                                            [
                                                'product_content_id' => $productContentId,
                                                'product_id'         => $productId
                                            ]);
                $alreadyInserted[] = $productId;
            }
        }
        
        return $this;
    }
    
    
    protected function _prepareProductContentDescriptionData($productContentId, $type)
    {
        $productContentDescriptionData = [];
        $postData                      = $this->_getPostData('content_manager')[$type];
        
        foreach ($this->descriptionFields as $field) {
            // do not loop over internal_name value
            if (is_array($postData[$field])) {
                foreach ($postData[$field] as $languageCode => $value) {
                    $languageId = $this->languageProvider->getIdByCode(new LanguageCode(new StringType($languageCode)));
                    
                    $productContentDescriptionData[$languageId]['product_content_id'] = $productContentId;
                    $productContentDescriptionData[$languageId]['language_id']        = $languageId;
                    
                    $productContentDescriptionData[$languageId][$field] = $value;
                }
            }
        }
        
        return $productContentDescriptionData;
    }
    
    
    protected function _prepareProductContentResourceData($type)
    {
        $productContentResourceData = [];
        $postData                   = $this->_getPostData('content_manager')[$type];
        
        if ($this->_isFilemanagerAvailable() === false && $type === 'file') {
            $postData['file'] = $this->_checkProductContentFileUploads();
        }
        
        foreach ($this->resourceFields as $field) {
            if (isset($postData[$field]) && is_array($postData[$field])) {
                foreach ($postData[$field] as $languageCode => $value) {
                    $languageId = $this->languageProvider->getIdByCode(new LanguageCode(new StringType($languageCode)));
                    
                    $productContentResourceData[$languageId]['resource'] = $value;
                }
            }
        }
        
        return $productContentResourceData;
    }
    
    
    protected function _insertIntoProductContentDescriptionsAndResources(
        array $productContentDescriptionData,
        array $productContentResourceData,
        $type
    ) {
        $types = [
            'file' => 1,
            'link' => 2,
            'text' => 3,
        ];
        
        foreach ($productContentDescriptionData as $key => $productContentDescriptionDataSet) {
            $this->queryBuilder->insert('product_content_descriptions', $productContentDescriptionDataSet);
            
            $productContentDescriptionId = $this->queryBuilder->insert_id();
            if (isset($productContentResourceData) && count($productContentResourceData) > 0) {
                $productContentResourceDataSet = $productContentResourceData[$key];
                
                $productContentResourceDataSet['product_content_description_id'] = $productContentDescriptionId;
                $productContentResourceDataSet['product_content_types_id']       = $types[$type];
                $this->queryBuilder->insert('product_content_resources', $productContentResourceDataSet);
            }
        }
        
        return $this;
    }
    
    
    protected function _updateIntoProductContentDescriptionsAndResources(
        array $productContentDescriptionData,
        array $productContentResourceData
    ) {
        $productContentId = $this->_getQueryParameter('id');
        
        foreach ($productContentDescriptionData as $key => $productContentDescriptionDataSet) {
            $this->queryBuilder->update('product_content_descriptions',
                                        $productContentDescriptionDataSet,
                                        [
                                            'product_content_id' => $productContentId,
                                            'language_id'        => $productContentDescriptionDataSet['language_id']
                                        ]);
            
            $productContentDescriptionId = $this->queryBuilder->select()
                                               ->from('product_content_descriptions')
                                               ->where([
                                                           'product_content_id' => $productContentId,
                                                           'language_id'        => $productContentDescriptionDataSet['language_id']
                                                       ])
                                               ->get()
                                               ->row_array()['id'];
            
            $productContentResourceDataSet = $productContentResourceData[$key];
            
            $this->queryBuilder->update('product_content_resources',
                                        $productContentResourceDataSet,
                                        ['product_content_description_id' => $productContentDescriptionId]);
        }
        
        return $this;
    }
    
    
    protected function _insertProductContentPostData($type)
    {
        $productContentId = $this->_insertIntoProductContentsAndReturnInsertId($type);
        
        $productContentDescriptionData = $this->_insertIntoProductContentProducts($productContentId, $type)
            ->_prepareProductContentDescriptionData($productContentId, $type);
        $productContentResourceData    = $this->_prepareProductContentResourceData($type);
        
        if (GROUP_CHECK && ($this->_getPostData('content_manager')[$type]['group_check'] ?? null)) {
            $this->queryBuilder->delete('product_content_customer_status', ['product_content_id' => $productContentId]);
            foreach ($this->_getPostData('content_manager')[$type]['group_check'] as $customerStatusId) {
                if ($customerStatusId !== 'all') {
                    $this->queryBuilder->insert('product_content_customer_status',
                                                [
                                                    'product_content_id' => $productContentId,
                                                    'customer_status_id' => $customerStatusId
                                                ]);
                }
            }
        }
        
        return $this->_insertIntoProductContentDescriptionsAndResources($productContentDescriptionData,
                                                                        $productContentResourceData,
                                                                        $type)
            ->_getUpdateResponse('ContentManagerProductContents', $productContentId);
    }
    
    
    /**
     * Return the data for the products content overview.
     *
     * @return array
     */
    protected function _getOverviewData()
    {
        $data = $this->queryBuilder->select('pc.id as group_id, pc.name as internal_name, pct.name as type, COUNT(pcp.product_id) as usage_count')
            ->from('product_contents as pc')
            ->join('product_content_products as pcp',
                   'pc.id = pcp.product_content_id',
                   'left')
            ->join('product_content_descriptions as pcd', 'pc.id = pcd.product_content_id')
            ->join('product_content_resources as pcr',
                   'pcd.id = pcr.product_content_description_id')
            ->join('product_content_types as pct', 'pct.id = pcr.product_content_types_id')
            ->where('language_id',
                    $_SESSION['languages_id'])// Todo: Replace $_SESSION with lang selection
            ->group_by('group_id, pcp.product_content_id, type', 'pc.id')
            ->get()
            ->result_array();
        
        // add description text
        foreach ($data as $key => $dataSet) {
            $data[$key]['description'] = $this->languageTextManager->get_text('DESCRIPTION_PRODUCT_CONTENT_'
                                                                              . strtoupper($dataSet['type']));
        }
        
        return $data;
    }
    
    
    /**
     * Returns the type of a file by its file extension.
     *
     * @param $file
     *
     * @return string
     */
    protected function _getFileType($file)
    {
        $fileExtension = substr($file, strrpos($file, '.') + 1);
        switch ($fileExtension) {
            case 'pdf':
                return 'pdf';
            case 'html':
            case 'php':
                return 'code';
            case 'txt':
            case 'doc':
            case 'docx':
                return 'text';
            case 'png':
            case 'jpg':
            case 'jpeg':
            case 'gif':
                return 'image';
            default:
                return 'unknown';
        }
    }
    
    
    protected function _getEditData()
    {
        $productContentEditData = $this->_getProductContentEditData();
        $products               = ['products' => $this->_getProductContentProducts()];
        $ckEditorData           = ['ckeditor' => $this->_getCkEditorData()];
        $formAction             = [
            'form_action' => 'admin.php?do=ContentManagerProductContents/update&id=' . $this->_getQueryParameter('id')
        ];
        
        $data = [
            $productContentEditData['type'] => array_merge($productContentEditData,
                                                           $products,
                                                           $ckEditorData,
                                                           $formAction),
            'filemanager_available'         => $this->_isFilemanagerAvailable(),
            'filelist'                      => $this->_getProductsContentFiles(),
            'type'                          => $productContentEditData['type'],
            'page_token'                    => $_SESSION['coo_page_token']->generate_token()
        ];
        
        return $data;
    }
    
    
    protected function _getProductContentProducts()
    {
        $queryResult = $this->queryBuilder->select('product_id')
            ->from('product_content_products')
            ->where('product_content_id',
                    $this->_getQueryParameter('id'))
            ->get()
            ->result_array();
        
        $products = [];
        foreach ($queryResult as $product) {
            $products[] = $product['product_id'];
        }
        
        return implode(',', $products);
    }
    
    
    protected function _getProductContentEditData()
    {
        $data               = [];
        $productContentId   = $this->_getQueryParameter('id');
        $productContentData = $this->queryBuilder->select('pc.name as internal_name, pcd.title, pcd.content, pcd.language_id, pcr.resource, pct.name as type')
            ->from('product_contents as pc')
            ->join('product_content_descriptions as pcd',
                   'pc.id = pcd.product_content_id')
            ->join('product_content_resources as pcr',
                   'pcd.id = pcr.product_content_description_id')
            ->join('product_content_types as pct',
                   'pcr.product_content_types_id = pct.id')
            ->where('pc.id', $productContentId)
            ->get()
            ->result_array();
        
        foreach ($productContentData as $productContentDataSet) {
            $languageCode = $this->languageProvider->getCodeById(new IdType($productContentDataSet['language_id']))
                ->asString();
            
            $data['internal_name']                               = $productContentDataSet['internal_name'];
            $data['type']                                        = $productContentDataSet['type'];
            $data['title'][$languageCode]                        = $productContentDataSet['title'];
            $data['content'][$languageCode]                      = $productContentDataSet['content'];
            $data[$productContentDataSet['type']][$languageCode] = $productContentDataSet['resource'];
        }
        
        return $data;
    }
    
    
    protected function _getCkEditorData()
    {
        $ckIdentifier = [];
        $ckTypes      = [];
        foreach ($this->languageProvider->getCodes()->getArray() as $languageCode) {
            $languageCode                = $languageCode->asString();
            $ckIdentifier[$languageCode] = 'content-manager-product-content-link-content-'
                                           . $this->_getQueryParameter('id') . '-' . $languageCode;
            $ckTypes[$languageCode]      = $this->userConfigurationService->getUserConfiguration(new IdType(0),
                                                                                                 $ckIdentifier[$languageCode]) ? : 'ckeditor';
        }
        
        return [
            'identifier' => $ckIdentifier,
            'type'       => $ckTypes
        ];
    }
    
    
    /**
     * Detects file uploads for file product contents and returns the array for new file post data.
     *
     * @return array
     */
    protected function _checkProductContentFileUploads()
    {
        $return = $this->_getPostData('content_manager')['file']['file'];
        if (count($_FILES['content_manager']['name']['file']['file']) > 0) {
            foreach ($_FILES['content_manager']['name']['file']['file'] as $key => $filename) {
                if (!empty($filename) && $_FILES['content_manager']['error']['file']['file'][$key] === 0) {
                    // move uploaded file into media/content directory
                    $directory    = DIR_FS_CATALOG . 'media' . DIRECTORY_SEPARATOR . 'products' . DIRECTORY_SEPARATOR;
                    $tempFilename = $_FILES['content_manager']['tmp_name']['file']['file'][$key];
                    move_uploaded_file($tempFilename, $directory . $filename);
                    
                    // update post data for selected content_file. set this value to the uploaded file.
                    $return[$key] = $filename;
                }
            }
        }
        
        return $return;
    }
	
	
	/**
	 * Verifies the page token and stops script if the token is invalid.
	 */
	protected function _verifyPageToken()
	{
		$_SESSION['coo_page_token']->is_valid($_POST['page_token']);
	}
}