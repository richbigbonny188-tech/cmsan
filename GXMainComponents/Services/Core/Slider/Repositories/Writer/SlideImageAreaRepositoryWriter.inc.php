<?php

/* --------------------------------------------------------------
   SlideImageAreaRepositoryWriter.inc.php 2016-10-28
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('SlideImageAreaRepositoryWriterInterface');

/**
 * Class SlideImageAreaRepositoryWriter
 *
 * @category   System
 * @package    Slider
 * @subpackage Repositories
 */
class SlideImageAreaRepositoryWriter implements SlideImageAreaRepositoryWriterInterface
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
     * SlideImageAreaRepositoryWriter constructor.
     *
     * @param CI_DB_query_builder $db Query builder.
     */
    public function __construct(CI_DB_query_builder $db)
    {
        $this->db = $db;
    }
    
    
    /**
     * Stores a SlideImageArea to the database.
     *
     * @param IdType                  $slideImageId
     * @param SlideImageAreaInterface $slideImageArea
     *
     * @return int ID of inserted slide image area or the used ID if the slide image area had an ID already.
     */
    public function store(IdType $slideImageId, SlideImageAreaInterface $slideImageArea)
    {
        $dataArray = [];
        
        $slideImageAreaId = $slideImageArea->getId();
        if ($slideImageAreaId !== 0) {
            $dataArray['id'] = $slideImageAreaId;
        }
        
        $dataArray['slide_images_id'] = $slideImageId->asInt();
        $dataArray['coordinates']     = $slideImageArea->getCoordinates();
        $dataArray['link_title']      = $slideImageArea->getLinkTitle();
        $dataArray['link_url']        = $slideImageArea->getLinkUrl();
        $dataArray['link_target']     = $slideImageArea->getLinkTarget();
        
        $this->db->insert($this->table, $dataArray);
        
        if ($slideImageAreaId === 0) {
            return $this->db->insert_id();
        } else {
            return $slideImageAreaId;
        }
    }
}