<?php
/* --------------------------------------------------------------
   ManufacturerWriter.inc.php 2018-01-23
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ManufacturerWriter
 *
 * @category   System
 * @package    Manufacturer
 * @subpackage Repositories
 */
class ManufacturerWriter implements ManufacturerWriterInterface
{
    /**
     * @var \CI_DB_query_builder
     */
    protected $queryBuilder;
    
    /**
     * @var \LanguageProvider
     */
    protected $languageProvider;
    
    
    /**
     * ManufacturerWriter constructor.
     *
     * @param \CI_DB_query_builder $queryBuilder
     * @param \LanguageProvider    $languageProvider
     */
    public function __construct(CI_DB_query_builder $queryBuilder, LanguageProvider $languageProvider)
    {
        $this->queryBuilder     = $queryBuilder;
        $this->languageProvider = $languageProvider;
    }
    
    
    /**
     * Saves manufacturer entity data in database.
     *
     * @param \ManufacturerInterface $manufacturer Manufacturer to be saved.
     *
     * @return $this|\ManufacturerWriterInterface Same instance for chained method calls.
     */
    public function store(ManufacturerInterface $manufacturer)
    {
        $dataArray = [
            'manufacturers_name'  => $manufacturer->getName(),
            'manufacturers_image' => $manufacturer->getImage(),
            'date_added'          => $manufacturer->getDateAdded()->format('Y-m-d H:i:s'),
            'last_modified'       => $manufacturer->getLastModified()->format('Y-m-d H:i:s')
        ];
        
        $this->queryBuilder->insert('manufacturers', $dataArray);
        $manufacturerId = $this->queryBuilder->insert_id();
        
        foreach ($manufacturer->getUrls() as $languageCode => $url) {
            $this->queryBuilder->insert('manufacturers_info',
                                        [
                                            'manufacturers_id'  => $manufacturerId,
                                            'languages_id'      => $this->languageProvider->getIdByCode(MainFactory::create('LanguageCode',
                                                                                                                            new StringType($languageCode))),
                                            'manufacturers_url' => $url
                                        ]);
        }
        $manufacturer->setId(new IdType($manufacturerId));
        
        return $this;
    }
    
    
    /**
     * Updates manufacturer entity data in database.
     *
     * @param \ManufacturerInterface $manufacturer Manufacturer to be updated.
     *
     * @return $this|\ManufacturerWriterInterface Same instance for chained method calls.
     */
    public function update(ManufacturerInterface $manufacturer)
    {
        $dataArray = [
            'manufacturers_name'  => $manufacturer->getName(),
            'manufacturers_image' => $manufacturer->getImage(),
        ];
        $this->queryBuilder->update('manufacturers', $dataArray, ['manufacturers_id' => $manufacturer->getId()]);
        $manufacturer->setLastModified(new DateTime());
        
        foreach ($manufacturer->getUrls() as $languageCode => $url) {
            $languageId = $this->languageProvider->getIdByCode(new LanguageCode(new StringType($languageCode)));
            $this->queryBuilder->update('manufacturers_info',
                                        ['manufacturers_url' => $url],
                                        ['manufacturers_id' => $manufacturer->getId(), 'languages_id' => $languageId]);
        }
        
        return $this;
    }
}
