<?php
/* --------------------------------------------------------------
  GmConfigurationRepository.php 2021-04-29
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2021 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

/**
 * Class GmConfigurationRepository
 */
class GmConfigurationRepository implements GmConfigurationRepositoryInterface
{
    /**
     * @var GmConfigurationReaderInterface
     */
    protected $reader;
    
    /**
     * @var GmConfigurationFactoryInterface
     */
    protected $factory;
    
    /**
     * @var GmConfigurationWriterInterface
     */
    protected $writer;
    
    
    /**
     * GmConfigurationRepository constructor.
     *
     * @param GmConfigurationReaderInterface  $reader
     * @param GmConfigurationWriterInterface  $writer
     * @param GmConfigurationFactoryInterface $factory
     */
    public function __construct(
        GmConfigurationReaderInterface $reader,
        GmConfigurationWriterInterface $writer,
        GmConfigurationFactoryInterface $factory
    ) {
        $this->reader  = $reader;
        $this->factory = $factory;
        $this->writer  = $writer;
    }
    
    
    /**
     * @param string $key
     *
     * @return GmConfigurationInterface
     * @throws GmConfigurationNotFoundException
     */
    public function getConfigurationByKey(string $key): GmConfigurationInterface
    {
        $result = $this->reader->getConfigurationByKey($key);
        
        $id        = (int)$result['id'];
        $key       = $result['key'];
        $value     = $result['value'];
        $sortOrder = (int)$result['sort_order'];
        
        return $this->factory->createGmConfiguration($id, $key, $value, 0, $sortOrder);
    }
    
    
    /**
     * @param GmConfigurationInterface $configuration
     */
    public function updateGmConfiguration(GmConfigurationInterface $configuration): void
    {
        $this->writer->updateGmConfiguration($configuration);
    }
}
