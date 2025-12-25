<?php
/* --------------------------------------------------------------
  GmConfigurationWriter.php 2021-04-29
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2021 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

/**
 * Class GmConfigurationWriter
 */
class GmConfigurationWriter implements GmConfigurationWriterInterface
{
    /**
     * @var CI_DB_query_builder
     */
    protected $queryBuilder;
    
    /**
     * @var string
     */
    protected const CONFIGURATION_TABLE = 'gx_configurations';
    
    
    /**
     * GmConfigurationWriter constructor.
     *
     * @param CI_DB_query_builder $queryBuilder
     */
    public function __construct(CI_DB_query_builder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }
    
    
    /**
     * @param GmConfigurationInterface $configuration
     */
    public function updateGmConfiguration(GmConfigurationInterface $configuration): void
    {
        $newData = [
            'value'      => $configuration->value(),
            'sort_order' => $configuration->sortOrder()
        ];
    
        $where = [
            'id' => $configuration->id()
        ];
        
        $this->queryBuilder->update(self::CONFIGURATION_TABLE, $newData, $where);
    }
}
