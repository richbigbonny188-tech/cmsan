<?php

/* --------------------------------------------------------------
   SlideRepositoryReader.inc.php 2016-09-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class SlideRepositoryReader
 *
 * @category   System
 * @package    Slider
 * @subpackage Repositories
 */
class SlideRepositoryReader implements SlideRepositoryReaderInterface
{
    /**
     * Table name
     *
     * @var string
     */
    protected $table = 'slides';
    
    /**
     * Query builder.
     *
     * @var CI_DB_query_builder
     */
    protected $db;
    
    
    /**
     * SlideRepositoryReader constructor.
     *
     * @param CI_DB_query_builder $db
     */
    public function __construct(CI_DB_query_builder $db)
    {
        $this->db = $db;
    }
    
    
    /**
     * Returns a SlideCollection for the given Slider ID.
     *
     * @param IdType $sliderId
     *
     * @return SlideCollection All slides found by the slider ID as a SlideCollection.
     * @throws InvalidArgumentException
     *
     * @throws UnexpectedValueException
     */
    public function getBySliderId(IdType $sliderId)
    {
        $slideArray = [];
        
        $result = $this->db->from($this->table)
            ->where(['slider_id' => $sliderId->asInt()])
            ->order_by('sort_order')
            ->get();
        
        foreach ($result->result_array() as $row) {
            $slide = MainFactory::create('Slide');
            
            $this->_setDbValues($slide, $row);
            
            $slideArray[] = $slide;
        }
        
        return MainFactory::create('SlideCollection', $slideArray);
    }
    
    
    /**
     * Returns a Slide instance by the given slide ID.
     *
     * @param IdType $slideId
     *
     * @return SlideInterface
     * @throws InvalidArgumentException
     *
     * @throws UnexpectedValueException
     */
    public function getById(IdType $slideId)
    {
        $slideData = $this->db->get_where($this->table, ['slide_id' => $slideId->asInt()])->row_array();
        
        if ($slideData === null) {
            throw new UnexpectedValueException('The requested slide was not found in the database (ID:'
                                               . $slideId->asInt() . ')');
        }
        
        $slide = MainFactory::create('Slide');
        $this->_setDbValues($slide, $slideData);
        
        return $slide;
    }
    
    
    /**
     * Returns a SlideCollection with all existing Slide objects by the given slider ID and language ID.
     *
     * @param IdType $sliderId
     * @param IdType $languageId
     *
     * @return SlideCollection
     * @throws InvalidArgumentException
     *
     * @throws UnexpectedValueException
     */
    public function getBySliderIdAndLanguageId(IdType $sliderId, IdType $languageId)
    {
        $slideArray = [];
        
        $result = $this->db->from($this->table)->where([
                                                           'slider_id'   => $sliderId->asInt(),
                                                           'language_id' => $languageId->asInt()
                                                       ])->order_by('sort_order')->get();
        
        foreach ($result->result_array() as $row) {
            $slide = MainFactory::create('Slide');
            
            $this->_setDbValues($slide, $row);
            
            $slideArray[] = $slide;
        }
        
        return MainFactory::create('SlideCollection', $slideArray);
    }
    
    
    /**
     * Check if an image file is used by another slide entry.
     *
     * @param FilenameStringType $filename Slide thumbnail image filename.
     * @param IdType             $slideId
     *
     * @return bool
     */
    public function isSlideThumbnailImageFileUsed(FilenameStringType $filename, IdType $slideId)
    {
        return $this->db->where('thumbnail', $filename->asString())
                   ->where('slide_id !=', $slideId->asInt())
                   ->get($this->table)
                   ->num_rows() > 0;
    }
    
    
    /*
     | -----------------------------------------------------------------------------------------------------------------
     | Helper Methods
     | -----------------------------------------------------------------------------------------------------------------
     */
    
    /**
     * Assign the slide values via the setters.
     *
     * @param Slide $slide Slide object.
     * @param array $row   Fetched DB row.
     *
     * @throws InvalidArgumentException If $row contains invalid values.
     */
    protected function _setDbValues(Slide $slide, array $row)
    {
        $slide->setId(new IdType($row['slide_id']));
        $slide->setLanguageId(new IdType($row['language_id']));
        $slide->setThumbnail(new StringType($row['thumbnail']));
        $slide->setTitle(new StringType($row['title']));
        $slide->setAltText(new StringType($row['alt_text']));
        $slide->setUrl(new StringType($row['url']));
        $slide->setUrlTarget(new StringType($row['url_target']));
        $slide->setSortOrder(new IntType($row['sort_order']));
    }
}