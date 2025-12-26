<?php

/* --------------------------------------------------------------
   SlideImageAreaRepositoryDeleter.inc.php 2016-10-28
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('SlideImageAreaRepositoryDeleterInterface');

/**
 * Class SlideImageAreaRepositoryDeleter
 *
 * @category   System
 * @package    Slider
 * @subpackage Repositories
 */
class SlideImageAreaRepositoryDeleter implements SlideImageAreaRepositoryDeleterInterface
{
    /**
     * Table name
     *
     * @var string
     */
    protected $table = 'slide_image_areas';
    
    /**
     * Query builder.
     *
     * @var CI_DB_query_builder
     */
    protected $db;
    
    
    /**
     * SlideImageAreaRepositoryDeleter constructor.
     *
     * @param CI_DB_query_builder $db Query builder.
     */
    public function __construct(CI_DB_query_builder $db)
    {
        $this->db = $db;
    }
    
    
    /**
     * Deletes a SlideImageArea by the given slide image area ID.
     *
     * @param IdType $slideImageAreaId
     *
     * @return SlideImageAreaRepositoryDeleterInterface Same instance for method chaining.
     */
    public function deleteById(IdType $slideImageAreaId)
    {
        $this->db->delete($this->table, ['id' => $slideImageAreaId->asInt()]);
        
        return $this;
    }
    
    
    /**
     * Deletes all SlideImageAreas by the given slide image ID.
     *
     * @param IdType $slideImageId
     *
     * @return SlideImageAreaRepositoryDeleterInterface Same instance for method chaining.
     */
    public function deleteBySlideImageId(IdType $slideImageId)
    {
        $this->db->delete($this->table, ['slide_images_id' => $slideImageId->asInt()]);
        
        return $this;
    }
}