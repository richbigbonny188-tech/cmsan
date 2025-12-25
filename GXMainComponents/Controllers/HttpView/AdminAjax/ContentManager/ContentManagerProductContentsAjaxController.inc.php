<?php

/* --------------------------------------------------------------
   ContentManagerProductContentsAjaxController.inc.php 2017-09-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ContentManagerProductContentsAjaxController
 *
 * Ajax controller for the content manager product contents
 *
 * @category   System
 * @package    AdminHttpViewControllers
 * @extends    AdminHttpViewController
 */
class ContentManagerProductContentsAjaxController extends AdminHttpViewController
{
    /**
     * Database connection.
     *
     * @var CI_DB_query_builder
     */
    protected $db;
    
    
    /**
     * Init
     */
    public function init()
    {
        $this->db = StaticGXCoreLoader::getDatabaseQueryBuilder();
    }
    
    
    /**
     * Deletes an content manager product content from the db
     *
     * @return bool|\JsonHttpControllerResponse
     * @throws \AuthenticationException
     */
    public function actionDeleteLink()
    {
        if (!$this->_isAdmin()) {
            throw new AuthenticationException('No admin privileges. Please contact the administrator.');
        }
        
        $id = $this->_getPostData('id');
        
        if (!isset($id) || !is_numeric($id)) {
            return MainFactory::create('JsonHttpControllerResponse', ['Invalid ID']);
        }
        
        $this->db->where('id', (int)$id)->delete('product_contents');
        
        $productContentsResources = $this->db->select('id')
            ->from('product_content_descriptions')
            ->where('product_content_id',
                    (int)$id)
            ->get()
            ->result_array();
        
        if (count($productContentsResources) > 0) {
            foreach ($productContentsResources as $resource) {
                $this->db->where('product_content_description_id',
                                 (int)$resource['id'])->delete('product_content_resources');
            }
        }
        
        $this->db->where('product_content_id', (int)$id)->delete('product_content_customer_status');
        $this->db->where('product_content_id', (int)$id)->delete('product_content_descriptions');
        $this->db->where('product_content_id', (int)$id)->delete('product_content_products');
        
        return MainFactory::create('JsonHttpControllerResponse', ['success']);
    }
    
    
    /**
     * @return bool|\JsonHttpControllerResponse
     * @throws \AuthenticationException
     */
    public function actionDeleteFile()
    {
        if (!$this->_isAdmin()) {
            throw new AuthenticationException('No admin privileges. Please contact the administrator.');
        }
        
        $id = $this->_getPostData('id');
        
        if (!isset($id) || !is_numeric($id)) {
            return MainFactory::create('JsonHttpControllerResponse', ['Invalid ID']);
        }
        
        $this->db->where('id', (int)$id)->delete('product_contents');
        
        $productContentsResources = $this->db->select('id')
            ->from('product_content_descriptions')
            ->where('product_content_id',
                    (int)$id)
            ->get()
            ->result_array();
        
        if (count($productContentsResources) > 0) {
            foreach ($productContentsResources as $resource) {
                $this->db->where('product_content_description_id',
                                 (int)$resource['id'])->delete('product_content_resources');
            }
        }
        
        $this->db->where('product_content_id', (int)$id)->delete('product_content_customer_status');
        $this->db->where('product_content_id', (int)$id)->delete('product_content_descriptions');
        $this->db->where('product_content_id', (int)$id)->delete('product_content_products');
        
        return MainFactory::create('JsonHttpControllerResponse', ['success']);
    }
    
    
    /**
     * Check if the customer is the admin.
     *
     * @return bool Is the customer the admin?
     */
    protected function _isAdmin()
    {
        try {
            $this->validateCurrentAdminStatus();
            
            return true;
        } catch (LogicException $exception) {
            return false;
        }
    }
}