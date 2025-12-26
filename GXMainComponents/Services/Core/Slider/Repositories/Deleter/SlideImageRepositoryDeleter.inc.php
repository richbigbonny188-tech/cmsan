<?php

/* --------------------------------------------------------------
   SlideImageRepositoryDeleter.inc.php 2016-08-08
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('SlideImageRepositoryDeleterInterface');

/**
 * Class SlideRepositoryDeleter
 *
 * @category   System
 * @package    Slider
 * @subpackage Repositories
 */
class SlideImageRepositoryDeleter implements SlideImageRepositoryDeleterInterface
{
    /**
     * Table name
     *
     * @var string
     */
    protected $table = 'slide_images';
    
    /**
     * Query builder.
     *
     * @var CI_DB_query_builder
     */
    protected $db;
    
    
    /**
     * SlideImageRepositoryDeleter constructor.
     *
     * @param CI_DB_query_builder $db Query builder.
     */
    public function __construct(CI_DB_query_builder $db)
    {
        $this->db = $db;
    }
    
    
    /**
     * Deletes a SlideImage by the given slide image ID.
     *
     * @param IdType $slideImageId
     *
     * @return SlideImageRepositoryDeleterInterface Same instance for method chaining.
     */
    public function deleteById(IdType $slideImageId)
    {
        $this->db->delete($this->table, ['slide_image_id' => $slideImageId->asInt()]);
        
        return $this;
    }
    
    
    /**
     * Deletes all SlideImage by the given Slide ID.
     *
     * @param IdType $slideId
     *
     * @return SlideImageRepositoryDeleterInterface Same instance for method chaining.
     */
    public function deleteBySlideId(IdType $slideId)
    {
        $this->db->delete($this->table, ['slide_id' => $slideId->asInt()]);
        
        return $this;
    }
}