<?php

/* --------------------------------------------------------------
   SliderRepositoryReader.inc.php 2016-08-30
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('SliderRepositoryReaderInterface');

/**
 * Class SliderRepositoryReader
 *
 * @category   System
 * @package    Slider
 * @subpackage Repositories
 */
class SliderRepositoryReader implements SliderRepositoryReaderInterface
{
    /**
     * Table name
     *
     * @var string
     */
    protected $table = 'sliders';
    
    /**
     * Query builder.
     *
     * @var CI_DB_query_builder
     */
    protected $db;
    
    
    /**
     * SliderRepositoryReader constructor.
     *
     * @param CI_DB_query_builder $db Query builder.
     */
    public function __construct(CI_DB_query_builder $db)
    {
        $this->db = $db;
    }
    
    
    /**
     * Returns a Slider instance by the given slider ID.
     *
     * @param IdType $sliderId
     *
     * @return SliderInterface
     * @throws UnexpectedValueException if no slider record for the provided slider ID was found.
     *
     */
    public function getById(IdType $sliderId)
    {
        $sliderData = $this->db->get_where($this->table, ['slider_id' => $sliderId->asInt()])->row_array();
        
        if ($sliderData === null) {
            throw new UnexpectedValueException('The requested slider was not found in database (ID:'
                                               . $sliderId->asInt() . ')');
        }
        
        $slider = MainFactory::create('Slider');
        $this->_setDbValues($slider, $sliderData);
        
        return $slider;
    }
    
    
    /**
     * Returns a SliderCollection with all existing Slider objects.
     *
     * @return SliderCollection
     */
    public function getAll()
    {
        $sliderArray = [];
        
        $result = $this->db->get($this->table)->result_array();
        
        foreach ($result as $row) {
            $slider = MainFactory::create('Slider');
            
            $this->_setDbValues($slider, $row);
            
            $sliderArray[] = $slider;
        }
        
        return MainFactory::create('SliderCollection', $sliderArray);
    }
    
    
    /**
     * Get the Slider for the start page.
     *
     * @return SliderInterface|null Returns the start page slider instance or null if no record was found.
     */
    public function getStartPageSlider()
    {
        $row = $this->db->get_where($this->table, ['start_page' => 1])->row_array();
        
        if (empty($row)) {
            return null; // No record matches the query.
        }
        
        $slider = MainFactory::create('Slider');
        
        $this->_setDbValues($slider, $row);
        
        return $slider;
    }
    
    
    /**
     * Assign the Slider values via the setter.
     *
     * @param Slider $slider
     * @param array  $row
     *
     * @throws InvalidArgumentException If $row contains invalid values.
     */
    protected function _setDbValues(Slider $slider, array $row)
    {
        $slider->setId(new IdType($row['slider_id']));
        $slider->setName(new StringType($row['name']));
        $slider->setSpeed(new DecimalType($row['speed']));
        $slider->setShowOnStartPage(new BoolType($row['start_page']));
    }
}