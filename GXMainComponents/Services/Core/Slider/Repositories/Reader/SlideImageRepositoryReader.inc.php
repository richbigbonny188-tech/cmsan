<?php

/* --------------------------------------------------------------
   SlideImageRepositoryReader.inc.php 2016-09-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('SlideImageRepositoryReaderInterface');

/**
 * Class SlideImageRepositoryReader
 *
 * @category   System
 * @package    Slider
 * @subpackage Repositories
 */
class SlideImageRepositoryReader implements SlideImageRepositoryReaderInterface
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
     * SlideImageRepositoryReader constructor.
     *
     * @param CI_DB_query_builder $db Query builder.
     */
    public function __construct(CI_DB_query_builder $db)
    {
        $this->db = $db;
    }
    
    
    /**
     * Returns a SlideImageCollection for the given Slide ID.
     *
     * @param IdType $slideId
     *
     * @return SlideImageCollection
     * @throws UnexpectedValueException if no slide image record for the provided slide ID was found.
     *
     */
    public function getBySlideId(IdType $slideId)
    {
        $slideImagesArray = [];
        
        $result = $this->db->get_where($this->table, ['slide_id' => $slideId->asInt()]);
        
        foreach ($result->result_array() as $row) {
            $slideImage = MainFactory::create('SlideImage');
            
            $this->_setDbValues($slideImage, $row);
            
            $slideImagesArray[] = $slideImage;
        }
        
        return MainFactory::create('SlideImageCollection', $slideImagesArray);
    }
    
    
    /**
     * Returns a SlideImage for the given SlideImage ID.
     *
     * @param IdType $slideImageId
     *
     * @return SlideImageInterface
     * @throws InvalidArgumentException
     *
     * @throws UnexpectedValueException
     */
    public function getById(IdType $slideImageId)
    {
        $slideImageData = $this->db->get_where($this->table, ['slide_image_id' => $slideImageId->asInt()])->row_array();
        
        if ($slideImageData === null) {
            throw new UnexpectedValueException('The requested slide image was not found in the database (ID:'
                                               . $slideImageId->asInt() . ')');
        }
        
        $slideImage = MainFactory::create('SlideImage');
        $this->_setDbValues($slideImage, $slideImageData);
        
        return $slideImage;
    }
    
    
    /**
     * Returns a SlideImageCollection with all existing SlideImage objects by the given slide ID and language ID.
     *
     * @param IdType $slideId
     * @param IdType $languageId
     *
     * @return SlideImageCollection
     * @throws InvalidArgumentException
     *
     * @throws UnexpectedValueException
     */
    public function getBySlideIdAndLanguageId(IdType $slideId, IdType $languageId)
    {
        $slideImagesArray = [];
        
        $result = $this->db->get_where($this->table,
                                       [
                                           'slide_id'    => $slideId->asInt(),
                                           'language_id' => $languageId->asInt()
                                       ]);
        
        foreach ($result->result_array() as $row) {
            $slideImage = MainFactory::create('SlideImage');
            
            $this->_setDbValues($slideImage, $row);
            
            $slideImagesArray[] = $slideImage;
        }
        
        return MainFactory::create('SlideImageCollection', $slideImagesArray);
    }
    
    
    /**
     * Check if an image file is used by another slide image entry.
     *
     * @param FilenameStringType $filename Slide image filename.
     * @param IdType             $slideImageId
     *
     * @return bool
     */
    public function isSlideImageFileUsed(FilenameStringType $filename, IdType $slideImageId)
    {
        return $this->db->where('image', $filename->asString())
                   ->where('slide_image_id !=', $slideImageId->asInt())
                   ->get($this->table)
                   ->num_rows() > 0;
    }
    
    
    /**
     * Assign the SlideImage values via the setter.
     *
     * @param SlideImage $slideImage
     * @param array      $row
     *
     * @throws InvalidArgumentException If $row contains invalid values.
     */
    protected function _setDbValues(SlideImage $slideImage, array $row)
    {
        $slideImage->setId(new IdType($row['slide_image_id']));
        $slideImage->setLanguageId(new IdType($row['language_id']));
        $slideImage->setBreakpoint(new NonEmptyStringType($row['breakpoint']));
        $slideImage->setImage(new StringType($row['image']));
    }
}