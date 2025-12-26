<?php
/* --------------------------------------------------------------
   ProductMediaContentController.inc.php 2018-11-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('HttpViewController');

/**
 * Class ProductMediaContentController
 *
 * @extends    HttpViewController
 * @category   System
 * @package    HttpViewControllers
 */
class ProductMediaContentController extends HttpViewController
{
    /**
     * Renders a content resource by the query parameter "pcdId".
     *
     * @return \HttpControllerResponse
     */
    public function actionDefault()
    {
        $result   = $this->_getProductContentDescriptionData();
        $isImage  = $this->_isImageFile($result['resource']);
        $filePath = $isImage ? DIR_WS_CATALOG . 'media/products/' . $result['resource'] : DIR_FS_CATALOG
                                                                                          . 'media/products/'
                                                                                          . $result['resource'];
        
        $content = $isImage ? xtc_image($filePath) : $this->_include($filePath);
        
        $html = <<<HTML
<html>
	<head>
		<title>{$result['title']}</title>
	</head>
	<body>
		$content
	</body>
</html>
HTML;
        
        return MainFactory::create('HttpControllerResponse', $html);
    }
    
    
    /**
     * Redirects to a product content resource (link) and increment the database field "hits".
     *
     * @return \RedirectHttpControllerResponse
     */
    public function actionLink()
    {
        return MainFactory::create('RedirectHttpControllerResponse',
                                   $this->_getProductContentDescriptionData()['resource']);
    }
    
    
    /**
     * Returns the product content description data by the query argument "pcdID".
     * The field product_content_descriptions.hits will be incremented by the value "1".
     *
     * @return array Product content description data array.
     */
    protected function _getProductContentDescriptionData()
    {
        $queryBuilder = StaticGXCoreLoader::getDatabaseQueryBuilder();
        
        $queryResult = $queryBuilder->select()
            ->from('product_content_descriptions as pcd')
            ->join('product_content_resources as pcr',
                   'pcd.id = pcr.product_content_description_id')
            ->where('pcd.id', $this->_getQueryParameter('pcdId'))
            ->get()
            ->row_array();
        
        $queryBuilder->update('product_content_descriptions',
                              ['hits' => (int)$queryResult['hits'] + 1],
                              ['id' => $queryResult['id']]);
        
        return $queryResult;
    }
    
    
    /**
     * Checks if the given file has an image file extension.
     * Detected image extensions are "gif", "jpg", "png", "tif" and "bmp".
     *
     * @param string $fileName File to be checked.
     *
     * @return bool Whether true if the given file has an image file extension and false otherwise.
     */
    protected function _isImageFile($fileName)
    {
        $imageExt = [
            'gif',
            'jpg',
            'png',
            'tif',
            'bmp',
        ];
        
        $fileExtension = substr($fileName, strpos($fileName, '.') + 1);
        
        return in_array($fileExtension, $imageExt);
    }
    
    
    /**
     * Includes a file by the given file name and returns their data.
     * If the given file do not exists, and empty string will be returned.
     *
     * @param string $fileName Path to included file.
     *
     * @return string Data of included file or empty string, if the file was not found.
     */
    protected function _include($fileName)
    {
        if (!is_file($fileName)) {
            return '';
        }
        
        ob_start();
        include $fileName;
        
        return ob_get_clean();
    }
}
