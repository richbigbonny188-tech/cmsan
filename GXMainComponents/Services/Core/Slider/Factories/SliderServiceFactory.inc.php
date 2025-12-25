<?php
/* --------------------------------------------------------------
   SliderServiceFactory.inc.php 2016-11-01
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class SliderServiceFactory
 *
 * @category   System
 * @package    Product
 * @subpackage Factories
 */
class SliderServiceFactory extends AbstractSliderServiceFactory
{
    /**
     * Database connection.
     *
     * @var CI_DB_query_builder
     */
    protected $db;
    
    
    /**
     * SliderServiceFactory constructor.
     *
     * @param CI_DB_query_builder $db Database connection.
     */
    public function __construct(CI_DB_query_builder $db)
    {
        $this->db = $db;
    }
    
    
    /**
     * Creates a slider read service.
     *
     * @return SliderReadServiceInterface
     */
    public function createSliderReadService()
    {
        $sliderRepository           = $this->_getSliderRepository();
        $slideRepository            = $this->_getSlideRepository();
        $slideImageRepository       = $this->_getSlideImageRepository();
        $slideImageAreaRepository   = $this->_getSlideImageAreaRepository();
        $sliderAssignmentRepository = $this->_getSliderAssignmentRepository();
        $slideImageFileStorage      = $this->_getSlideImageFileStorage();
        
        return MainFactory::create('SliderReadService',
                                   $sliderRepository,
                                   $slideRepository,
                                   $slideImageRepository,
                                   $slideImageAreaRepository,
                                   $sliderAssignmentRepository,
                                   $slideImageFileStorage);
    }
    
    
    /**
     * Creates a slider write service.
     *
     * @return SliderWriteServiceInterface
     */
    public function createSliderWriteService()
    {
        $sliderRepository           = $this->_getSliderRepository();
        $slideRepository            = $this->_getSlideRepository();
        $slideImageRepository       = $this->_getSlideImageRepository();
        $slideImageAreaRepository   = $this->_getSlideImageAreaRepository();
        $sliderAssignmentRepository = $this->_getSliderAssignmentRepository();
        $slideImageFileStorage      = $this->_getSlideImageFileStorage();
        
        return MainFactory::create('SliderWriteService',
                                   $sliderRepository,
                                   $slideRepository,
                                   $slideImageRepository,
                                   $slideImageAreaRepository,
                                   $sliderAssignmentRepository,
                                   $slideImageFileStorage);
    }
    
    
    /**
     * Creates a slider repository instance.
     *
     * @return SliderRepositoryInterface
     */
    protected function _getSliderRepository()
    {
        $sliderRepositoryWriter  = MainFactory::create('SliderRepositoryWriter', $this->db);
        $sliderRepositoryReader  = MainFactory::create('SliderRepositoryReader', $this->db);
        $sliderRepositoryDeleter = MainFactory::create('SliderRepositoryDeleter', $this->db);
        $slideRepository         = $this->_getSlideRepository();
        
        $sliderRepository = MainFactory::create('SliderRepository',
                                                $sliderRepositoryWriter,
                                                $sliderRepositoryReader,
                                                $sliderRepositoryDeleter,
                                                $slideRepository);
        
        return $sliderRepository;
    }
    
    
    /**
     * Creates a slide repository instance.
     *
     * @return SlideRepositoryInterface
     */
    protected function _getSlideRepository()
    {
        $slideRepositoryWriter  = MainFactory::create('SlideRepositoryWriter', $this->db);
        $slideRepositoryReader  = MainFactory::create('SlideRepositoryReader', $this->db);
        $slideRepositoryDeleter = MainFactory::create('SlideRepositoryDeleter', $this->db);
        $slideImageRepository   = $this->_getSlideImageRepository();
        
        $slideRepository = MainFactory::create('SlideRepository',
                                               $slideRepositoryWriter,
                                               $slideRepositoryReader,
                                               $slideRepositoryDeleter,
                                               $slideImageRepository);
        
        return $slideRepository;
    }
    
    
    /**
     * Creates a slide image repository instance.
     *
     * @return SlideImageRepositoryInterface
     */
    protected function _getSlideImageRepository()
    {
        $slideImageRepositoryWriter  = MainFactory::create('SlideImageRepositoryWriter', $this->db);
        $slideImageRepositoryReader  = MainFactory::create('SlideImageRepositoryReader', $this->db);
        $slideImageRepositoryDeleter = MainFactory::create('SlideImageRepositoryDeleter', $this->db);
        $slideImageAreaRepository    = $this->_getSlideImageAreaRepository();
        
        $slideImageRepository = MainFactory::create('SlideImageRepository',
                                                    $slideImageRepositoryWriter,
                                                    $slideImageRepositoryReader,
                                                    $slideImageRepositoryDeleter,
                                                    $slideImageAreaRepository);
        
        return $slideImageRepository;
    }
    
    
    /**
     * Creates a slide image area repository instance.
     *
     * @return SlideImageAreaRepositoryInterface
     */
    protected function _getSlideImageAreaRepository()
    {
        $slideImageAreaRepositoryWriter  = MainFactory::create('SlideImageAreaRepositoryWriter', $this->db);
        $slideImageAreaRepositoryReader  = MainFactory::create('SlideImageAreaRepositoryReader', $this->db);
        $slideImageAreaRepositoryDeleter = MainFactory::create('SlideImageAreaRepositoryDeleter', $this->db);
        
        $slideImageAreaRepository = MainFactory::create('SlideImageAreaRepository',
                                                        $slideImageAreaRepositoryWriter,
                                                        $slideImageAreaRepositoryReader,
                                                        $slideImageAreaRepositoryDeleter);
        
        return $slideImageAreaRepository;
    }
    
    
    /**
     * Creates a slider assignment repository instance.
     *
     * @return SliderAssignmentRepositoryInterface
     */
    protected function _getSliderAssignmentRepository()
    {
        $slideAssignmentRepository = MainFactory::create('SliderAssignmentRepository', $this->db);
        
        return $slideAssignmentRepository;
    }
    
    
    /**
     * Create a slide image file storage instance.
     *
     * @return SlideImageFileStorage
     */
    protected function _getSlideImageFileStorage()
    {
        $envSlideImageFileStorageSettings = MainFactory::create('EnvSlideImageFileStorageSettings');
        $slideImageFileStorage            = MainFactory::create('SlideImageFileStorage',
                                                                $envSlideImageFileStorageSettings);
        
        return $slideImageFileStorage;
    }
}