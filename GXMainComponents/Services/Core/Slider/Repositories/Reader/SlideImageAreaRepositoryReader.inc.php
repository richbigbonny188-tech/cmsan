<?php

/* --------------------------------------------------------------
   SlideImageAreaRepositoryReader.inc.php 2016-12-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('SlideImageAreaRepositoryReaderInterface');

/**
 * Class SlideImageAreaRepositoryReader
 *
 * @category   System
 * @package    Slider
 * @subpackage Repositories
 */
class SlideImageAreaRepositoryReader implements SlideImageAreaRepositoryReaderInterface
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
     * SlideImageAreaRepositoryReader constructor.
     *
     * @param CI_DB_query_builder $db Query builder.
     */
    public function __construct(CI_DB_query_builder $db)
    {
        $this->db = $db;
    }
    
    
    /**
     * Returns a SlideImageAreaCollection instance by the given slide image ID.
     *
     * @param IdType $slideImageId ID of the slide image to get.
     *
     * @return SlideImageAreaCollection
     */
    public function getBySlideImageId(IdType $slideImageId)
    {
        $slideImageAreasArray = [];
        
        $result = $this->db->get_where($this->table, ['slide_images_id' => $slideImageId->asInt()]);
        
        foreach ($result->result_array() as $row) {
            $slideImageArea = MainFactory::create('SlideImageArea');
            
            $this->_setDbValues($slideImageArea, $row);
            
            $slideImageAreasArray[] = $slideImageArea;
        }
        
        return MainFactory::create('SlideImageAreaCollection', $slideImageAreasArray);
    }
    
    
    /**
     * Returns a SlideImageArea instance by the given slide image area ID.
     *
     * @param IdType $slideImageAreaId
     *
     * @return SlideImageAreaInterface
     * @throws InvalidArgumentException
     *
     * @throws UnexpectedValueException
     */
    public function getById(IdType $slideImageAreaId)
    {
        $slideImageAreaData = $this->db->get_where($this->table, ['id' => $slideImageAreaId->asInt()])->row_array();
        
        if ($slideImageAreaData === null) {
            throw new UnexpectedValueException('The requested slide image area was not found in the database (ID:'
                                               . $slideImageAreaId->asInt() . ')');
        }
        
        $slideImageArea = MainFactory::create('SlideImageArea');
        $this->_setDbValues($slideImageArea, $slideImageAreaData);
        
        return $slideImageArea;
    }
    
    
    /**
     * Assign the SlideImageArea values via the setter.
     *
     * @param SlideImageArea $slideImageArea
     * @param array          $row
     *
     * @throws InvalidArgumentException If $row contains invalid values.
     */
    protected function _setDbValues(SlideImageArea $slideImageArea, array $row)
    {
        $slideImageArea->setId(new IdType($row['id']));
        $slideImageArea->setLinkTitle(new StringType((string)$row['link_title']));
        $slideImageArea->setLinkUrl(new StringType($row['link_url']));
        $slideImageArea->setLinkTarget(new NonEmptyStringType($row['link_target']));
        $slideImageArea->setCoordinates(new NonEmptyStringType($row['coordinates']));
    }
}