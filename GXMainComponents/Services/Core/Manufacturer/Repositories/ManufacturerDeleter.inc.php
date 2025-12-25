<?php
/* --------------------------------------------------------------
   ManufacturerDeleter.inc.php 2019-06-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ManufacturerDeleter
 *
 * @category   System
 * @package    Manufacturer
 * @subpackage Repositories
 */
class ManufacturerDeleter implements ManufacturerDeleterInterface
{
    /**
     * @var \CI_DB_query_builder
     */
    protected $queryBuilder;
    
    
    /**
     * ManufacturerDeleter constructor.
     *
     * @param \CI_DB_query_builder $queryBuilder
     */
    public function __construct(CI_DB_query_builder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }
    
    
    /**
     * Deletes manufacturer entity data in database.
     *
     * @param \ManufacturerInterface $manufacturer manufacturer entity to be delete.
     *
     * @return $this|\ManufacturerDeleterInterface Same instance for chained method calls.
     */
    public function delete(ManufacturerInterface $manufacturer)
    {
        $this->queryBuilder->delete('manufacturers', ['manufacturers_id' => $manufacturer->getId()]);
        $this->queryBuilder->delete('manufacturers_info', ['manufacturers_id' => $manufacturer->getId()]);
        $this->queryBuilder->update('products',
                                    ['manufacturers_id' => null],
                                    ['manufacturers_id' => $manufacturer->getId()]);
        
        return $this;
    }
}