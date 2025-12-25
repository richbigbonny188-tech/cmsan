<?php
/* --------------------------------------------------------------
   PropertiesAdminControl.inc.php 2021-10-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

require_once DIR_FS_CATALOG . 'gm/inc/gm_check_image_upload.inc.php';

class PropertiesAdminControl
{
    /**
     * @var ResponsiveFileManagerConfigurationStorage
     */
    protected $v_file_manager_configuration;


    public function __construct()
    {
    }
    
    public function get_all_properties()
    {
        $coo_product_properties_struct_supplier = MainFactory::create_object('ProductPropertiesStructSupplier');
        $all_properties = $coo_product_properties_struct_supplier->get_all_properties();
        return $all_properties;
    }
    
    public function get_properties($p_properties_id)
    {
        $coo_product_properties_struct_supplier = MainFactory::create_object('ProductPropertiesStructSupplier');
        $properties = $coo_product_properties_struct_supplier->get_properties($p_properties_id);
        return $properties;
    }
    
    public function get_properties_values_by_properties_values_id($p_properties_values_id)
    { 
        $coo_product_properties_struct_supplier = MainFactory::create_object('ProductPropertiesStructSupplier');
        $properties_values = $coo_product_properties_struct_supplier->get_properties_values_by_properties_values_id($p_properties_values_id);
        return $properties_values;
    }
    /**
     * @return bool
     */
    public function use_file_manager() : bool
    {
        if ($this->v_file_manager_configuration === null) {
            $this->v_file_manager_configuration = MainFactory::create('ResponsiveFileManagerConfigurationStorage');
        }
        return $this->v_file_manager_configuration->isInstalled()
               && $this->v_file_manager_configuration->get('use_in_property_pages');
        
    }
    
    
    public function save_properties($p_properties_data)
    {
        $c_properties_data = $p_properties_data;
        if(!is_array($c_properties_data)) trigger_error('save_properties: typeof($p_properties_data) != array', E_USER_ERROR); 
        
        $t_return = array();
        
        $t_insert_mode = true;
        $t_properties_id = (int)$c_properties_data['properties_id'];
        $t_sort_order = (int)$c_properties_data['sort_order'];
        
        $coo_properties = new GMDataObject('properties');
        
        if(empty($t_properties_id))
        {
            $t_return['action'] = 'insert_properties';
            $coo_properties->set_keys(array('properties_id' => false));
        } 
        else 
        { 
            $t_insert_mode = false;
            $t_return['action'] = 'update_properties';
            $coo_properties->set_keys(array('properties_id' => $t_properties_id));
        }
        $coo_properties->set_data_value('sort_order', $t_sort_order);
        
        if (isset($p_properties_data['display-type'])) {
            
            $coo_properties->set_data_value('display_type', $p_properties_data['display-type']);
        }
        
        $t_insert_id = $coo_properties->save_body_data();
        if($t_insert_id > 0) $t_properties_id = $t_insert_id;

        // save properties description
	    $t_languages_array   = xtc_get_languages();
	    $db                  = StaticGXCoreLoader::getDatabaseQueryBuilder();
	    $languageProvider    = MainFactory::create('LanguageProvider', $db);
	    $defaultLanguageCode = $languageProvider->getDefaultLanguageCode();

        for($i=0, $iMax = count($t_languages_array); $i < $iMax; $i++)
        {
            $coo_properties_description = new GMDataObject('properties_description');

            $t_language_id 	 = $t_languages_array[$i]['id'];
            $t_language_code = $t_languages_array[$i]['code'];
	
	        $propertiesName      = $c_properties_data['properties_name'][$defaultLanguageCode];
	        $propertiesAdminName = $c_properties_data['properties_admin_name'][$defaultLanguageCode];
            if(array_key_exists($t_language_code, $c_properties_data['properties_name']))
            {
	            $propertiesName      = $c_properties_data['properties_name'][$t_language_code];
            }
            if(array_key_exists($t_language_code, $c_properties_data['properties_admin_name']))
            {
	            $propertiesAdminName = $c_properties_data['properties_admin_name'][$t_language_code];
            }
	
	        if($t_insert_mode)
            {
                $coo_properties_description->set_keys(array('properties_description_id' => false));
                $coo_properties_description->set_data_value('properties_id', $t_properties_id);
                $coo_properties_description->set_data_value('language_id', $t_language_id);
	            $coo_properties_description->set_data_value('properties_name', $propertiesName);
	            $coo_properties_description->set_data_value('properties_admin_name', $propertiesAdminName);
            }
            else
            {
	            if((bool)$t_languages_array[$i]['status_admin'] !== true)
	            {
		            continue;
	            }
                $coo_properties_description->set_keys(array('properties_id' => $t_properties_id, 'language_id'	=> $t_language_id));
	            $coo_properties_description->set_data_value('properties_name', $propertiesName);
	            $coo_properties_description->set_data_value('properties_admin_name', $propertiesAdminName);
            }
	
            $coo_properties_description->save_body_data();
        }
        
        return $t_properties_id;
    }
    
    public function delete_properties($p_properties_id)
    {
        $c_properties_id = (int)$p_properties_id;
        if(empty($c_properties_id)) trigger_error('delete_properties: typeof($p_properties_id) != integer', E_USER_ERROR); 
        
        $t_return = array();
        
	    # get related properties_values 
	    $query = 'SELECT properties_values_id 
					FROM properties_values 
					WHERE properties_id = ' . $c_properties_id;
	    $result = xtc_db_query($query);
	
	    while($row = xtc_db_fetch_array($result)) {
		    # delete related properties_values
		    $this->delete_properties_values($row['properties_values_id']);
	    }
	
	    # delete properties_description
	    xtc_db_query('DELETE FROM properties_description WHERE properties_id = ' . $c_properties_id);
	
	    # delete property
	    xtc_db_query('DELETE FROM properties WHERE properties_id = ' . $c_properties_id);
        
        $t_return['action'] = 'delete_properties';
        $t_return['properties_id'] = $c_properties_id;
        
        return $t_return;
    }
    
    
    /**
     * @param array $p_properties_values_data
     *
     * @return string
     */
    protected function get_display_image_filename(array $p_properties_values_data): string
    {
        $c_properties_values_data = $p_properties_values_data;
        $gm_filename = '';
        if ($this->use_file_manager()) {
            $gm_filename = $c_properties_values_data['gm_image_upload'];
        } else {
            if (gm_check_image_upload('gm_image_upload') && $gm_upload_file = &xtc_try_upload('gm_image_upload',
                                                                                              DIR_FS_CATALOG_IMAGES
                                                                                              . 'product_images/property_images/')) {
                $gm_filename = $gm_upload_file->filename;
            }
        }
        
        return $gm_filename;
    }
    
    
    /**
     * @param $p_properties_values_data
     *
     * @return array
     */
    public function save_properties_values($p_properties_values_data) : array
    {
        $c_properties_values_data = $p_properties_values_data;
        if(!is_array($c_properties_values_data)) trigger_error('save_properties_values: typeof($p_properties_values_data) != array', E_USER_ERROR); 
        
        $t_return = array();
        
        $t_insert_mode = true;
        
        $t_properties_values_id = (int)$c_properties_values_data['properties_values_id'];
        
        if(empty($t_properties_values_id))
        {
            $t_return['action'] = 'insert_properties_values';
	
	        $query = 'INSERT INTO `properties_values` 
						SET 
							`properties_id` = ' . (int)$c_properties_values_data['properties_id'] . ',
							`sort_order` = ' . (int)$c_properties_values_data['sort_order'] . ',
							`value_model` = "' . xtc_db_input(htmlspecialchars_wrapper($c_properties_values_data['value_model'])) . '",
							`value_price` = ' . clean_numeric_input($c_properties_values_data['value_price']).',
							`display_image` = "' . $this->get_display_image_filename($c_properties_values_data) . '"';
	        xtc_db_query($query);
	        $t_properties_values_id = xtc_db_insert_id();
        } 
        else
        {
            $t_insert_mode = false;
            $t_return['action'] = 'update_properties_values';
            $t_property_value = $this->get_properties_values_by_properties_values_id($t_properties_values_id);
	
	        $query = 'UPDATE `properties_values` 
						SET 
							`properties_id` = ' . (int)$c_properties_values_data['properties_id'] . ',
							`sort_order` = ' . (int)$c_properties_values_data['sort_order'] . ',
							`value_model` = "' . xtc_db_input(htmlspecialchars_wrapper($c_properties_values_data['value_model'])) . '",
							`value_price` = ' . clean_numeric_input($c_properties_values_data['value_price']) . ',
							`display_image` = "' . $this->get_display_image_filename($c_properties_values_data) . '"
						WHERE `properties_values_id` = ' . $t_properties_values_id;
	        xtc_db_query($query);
        }

        // save properties values description
        $t_languages_array = xtc_get_languages();

        for($i=0, $iMax = count($t_languages_array); $i < $iMax; $i++)
        {
            $t_language_id 	 = (int)$t_languages_array[$i]['id'];
            $t_language_code = $t_languages_array[$i]['code'];

            if($t_insert_mode)
            {
	            if(array_key_exists($t_language_code, $c_properties_values_data['values_name']))
	            {
		            $valuesName = $c_properties_values_data['values_name'][$t_language_code];
	            }
	            else
	            {
		            $db               = StaticGXCoreLoader::getDatabaseQueryBuilder();
		            $languageProvider = MainFactory::create('LanguageProvider', $db);
		            $valuesName       = $c_properties_values_data['values_name'][$languageProvider->getDefaultLanguageCode()];
	            }
            	$query = 'INSERT INTO `properties_values_description`
							SET
								`properties_values_id` = ' . $t_properties_values_id . ',
								`language_id` = ' . $t_language_id . ',
								`values_name` = "' . xtc_db_input($valuesName) . '"';
	            xtc_db_query($query);
            }
            else
            {
	            if((bool)$t_languages_array[$i]['status_admin'] !== true)
	            {
		            continue;
	            }
	            $query = 'UPDATE `properties_values_description`
							SET
								`values_name` = "' . xtc_db_input($c_properties_values_data['values_name'][$t_language_code]) . '"
							WHERE
								`properties_values_id` = ' . $t_properties_values_id . ' AND 
								`language_id` = ' . $t_language_id;
	            xtc_db_query($query);
            }
        }

        if(!$t_insert_mode)
        {
            if((double)$t_property_value['value_price'] !== (double)$c_properties_values_data['value_price'])
            {
                $t_property_combis_admin_contol = new PropertiesCombisAdminControl();
                $t_combi_ids_array              = $t_property_combis_admin_contol->get_combi_ids_by_property_value_id($t_properties_values_id);

                $t_product_properties = new ProductPropertiesStructSupplier();
                foreach($t_combi_ids_array as $t_combi_id)
                {
                    $t_product_id = (int)$t_property_combis_admin_contol->get_product_id_by_combi_id($t_combi_id);
                    if($t_product_id === 0)
                    {
                        continue;
                    }
                    $t_properties_combis_array                      = $t_product_properties->get_combis($t_product_id,
                        $t_combi_id,
                        $_SESSION['languages_id']);
                    $t_properties_combis_array['properties_values'] = array_keys($t_properties_combis_array['combis_values']);

                    $t_properties_combis_array['products_id'] = $t_product_id;
                    $t_property_combis_admin_contol->save_combis($t_properties_combis_array, false, PRICE_IS_BRUTTO === 'true');
                }
            }
        }
        
        $t_return['properties_id'] = (int)$c_properties_values_data['properties_id'];        
        $t_return['properties_values_id'] = $t_properties_values_id;             
        
        return $t_return;
    }
    
    public function delete_properties_values($p_properties_values_id)
    {
        $c_properties_values_id = (int)$p_properties_values_id;
        if(empty($c_properties_values_id)) trigger_error('delete_properties_values: typeof($p_properties_values_id) != integer', E_USER_ERROR);  
        
        $t_return = array();
        
	    $combiIds = [];
	    
        $query = 'SELECT products_properties_combis_id 
					FROM products_properties_index 
					WHERE properties_values_id = ' . $c_properties_values_id;
        $result = xtc_db_query($query);
        
        while($row = xtc_db_fetch_array($result)) {
        	$combiIds[] = $row['products_properties_combis_id'];
        }
	
        if(count($combiIds)) {
	        # delete products_properties_combis_values
	        $propertiesCombisAdminControl = MainFactory::create_object('PropertiesCombisAdminControl');
	        $propertiesCombisAdminControl->delete_combis($combiIds);
	
	        #delete index entry
	        xtc_db_query('DELETE FROM products_properties_index WHERE products_properties_combis_id IN (' . implode(',', $combiIds) . ')');
        }
	
	    # delete properties_values_description
        xtc_db_query('DELETE FROM properties_values_description WHERE properties_values_id = ' . $c_properties_values_id);
	
	    # delete properties_values
        xtc_db_query('DELETE FROM properties_values WHERE properties_values_id = ' . $c_properties_values_id);
	    
        $t_return['properties_values_id'] = $c_properties_values_id;
        $t_return['action'] = 'delete_properties_values';
        
        return $t_return;
    }
    
    function get_properties_in_combis_count($p_properties_id)
    {
        $c_properties_id = (int)$p_properties_id;
        if(empty($c_properties_id)) trigger_error('get_properties_in_combis_count: typeof($p_properties_id) != integer', E_USER_ERROR);  
        
        $t_return = array();

        $t_count_combis = 0;
        
        $t_sql = '
            SELECT 
                count(*) AS countCombis
            FROM
                products_properties_index
            WHERE
                language_id = '.$_SESSION['languages_id'].' AND
                properties_id = '.$c_properties_id.'
        ';
        $t_result = xtc_db_query($t_sql);
        
        if(xtc_db_num_rows($t_result) == 1)
        {
            $t_row = xtc_db_fetch_array($t_result);
            $t_count_combis = $t_row['countCombis'];
        }
        
        $t_return['combis_count'] = $t_count_combis;
        $t_return['properties_id'] = $c_properties_id;
        
        return $t_return;
    }
    
    function get_properties_values_in_combis_count($p_properties_values_id)
    {
        $c_properties_values_id = (int)$p_properties_values_id;
        if(empty($c_properties_values_id)) trigger_error('get_properties_values_in_combis_count: typeof($p_properties_values_id) != integer', E_USER_ERROR);
    
        $t_return = array();
        
        $t_count_combis = 0;
        
        $t_sql = '
            SELECT 
                count(*) AS countCombis
            FROM
                products_properties_combis_values
            WHERE
                properties_values_id = '.$c_properties_values_id.'
        ';
        $t_result = xtc_db_query($t_sql);
        
        if(xtc_db_num_rows($t_result) == 1)
        {
            $t_row = xtc_db_fetch_array($t_result);
            $t_count_combis = $t_row['countCombis'];
        }

        $t_return['combis_count'] = $t_count_combis;
        $t_return['properties_values_id'] = $c_properties_values_id;
        
        return $t_return;
    }
}
