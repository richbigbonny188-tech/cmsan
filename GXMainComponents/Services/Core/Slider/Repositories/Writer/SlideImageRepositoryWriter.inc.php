<?php

/* --------------------------------------------------------------
   SlideImageRepositoryWriter.inc.php 2016-08-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('SlideImageRepositoryWriterInterface');

/**
 * Class SlideImageRepositoryWriter
 *
 * @category   System
 * @package    Slider
 * @subpackage Repositories
 */
class SlideImageRepositoryWriter implements SlideImageRepositoryWriterInterface
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
     * SlideImageRepositoryWriter constructor.
     *
     * @param CI_DB_query_builder $db Query builder.
     */
    public function __construct(CI_DB_query_builder $db)
    {
        $this->db = $db;
    }
    
    
    /**
     * Inserts a slide image to the database.
     *
     * @param IdType              $slideId
     * @param SlideImageInterface $slideImage
     *
     * @return int ID of inserted slide or the given slide ID if the slide had an ID already.
     */
    public function store(IdType $slideId, SlideImageInterface $slideImage)
    {
        $dataArray = [];
        
        $slideImageId = $slideImage->getId();
        if ($slideImageId !== 0) {
            $dataArray['slide_image_id'] = $slideImageId;
        }
        
        $dataArray['slide_id']    = $slideId->asInt();
        $dataArray['language_id'] = $slideImage->getLanguageId();
        $dataArray['breakpoint']  = $slideImage->getBreakpoint();
        $dataArray['image']       = $slideImage->getImage();
        
        $this->db->insert($this->table, $dataArray);
        
        if ($slideImageId === 0) {
            return $this->db->insert_id();
        } else {
            return $slideImageId;
        }
    }
    
    
    /**
     * Unset the image filename references in other slide image entry by the given filename.
     *
     * @param FilenameStringType $filename Slide image filename.
     *
     * @return SlideImageRepositoryWriterInterface Same instance for method chaining.
     */
    public function unsetSlideImageReference(FilenameStringType $filename)
    {
        $this->db->update($this->table, ['image' => ''], ['image' => $filename->asString()]);
        
        return $this;
    }
}