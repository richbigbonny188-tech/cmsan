<?php
/* --------------------------------------------------------------
   GXParcelServiceWriter.inc.php 2018-07-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ParcelServiceWriter
 */
class GXParcelServiceWriter implements ParcelServiceWriterInterface
{
    /**
     * @var \CI_DB_query_builder
     */
    protected $db;
    
    /**
     * @var \LanguageProvider
     */
    protected $languageProvider;
    
    
    /**
     * GXParcelServiceWriter constructor.
     *
     * @param \CI_DB_query_builder $db
     */
    public function __construct(\CI_DB_query_builder $db)
    {
        $this->db = $db;
    }
    
    
    /**
     * Inserts the given parcel service data.
     *
     * @param array $parcelService
     *
     * @return int parcel service ID
     */
    public function insert(array $parcelService)
    {
        $this->_updateDefaultIfNecessary($parcelService);
        
        $this->db->insert('parcel_services',
                          [
                              'name'    => $parcelService['name'],
                              'default' => $parcelService['default']
                          ]);
        $parcelServiceId = $this->db->insert_id();
        
        foreach ($parcelService['descriptions'] as $parcelServiceDescription) {
            $this->db->insert('parcel_services_description',
                              [
                                  'parcel_service_id' => $parcelServiceId,
                                  'language_id'       => $parcelServiceDescription['language_id'],
                                  'url'               => $parcelServiceDescription['url'],
                                  'comment'           => $parcelServiceDescription['comment']
                              ]);
        }
        
        return $parcelServiceId;
    }
    
    
    /**
     * Updates the given parcel service data.
     *
     * @param array   $parcelService   Parcel service data.
     * @param \IdType $parcelServiceId Id of updated parcel service.
     *
     * @return void
     */
    public function update(array $parcelService, IdType $parcelServiceId)
    {
        $this->_updateDefaultIfNecessary($parcelService);
        
        $this->db->update('parcel_services',
                          [
                              'name'    => $parcelService['name'],
                              'default' => $parcelService['default']
                          ],
                          ['parcel_service_id' => $parcelServiceId->asInt()]);
        
        foreach ($parcelService['descriptions'] as $parcelServiceDescription) {
            $descriptionData = [
                'parcel_service_id' => $parcelServiceId,
                'url'               => $parcelServiceDescription['url'],
                'comment'           => $parcelServiceDescription['comment']
            ];
            $where           = [
                'parcel_service_id' => $parcelServiceId->asInt(),
                'language_id'       => $parcelServiceDescription['language_id']
            ];
            
            $this->db->update('parcel_services_description', $descriptionData, $where);
        }
    }
    
    
    /**
     * Deletes the parcel service identified by given ID.
     *
     * @param \IdType $parcelServiceId
     */
    public function delete(\IdType $parcelServiceId)
    {
        $where = ['parcel_service_id' => $parcelServiceId];
        $this->db->delete('parcel_services', $where);
        $this->db->delete('parcel_services_description', $where);
    }
    
    
    protected function _updateDefaultIfNecessary(array $parcelServiceData)
    {
        if ((int)$parcelServiceData['default'] === 1) {
            $this->db->update('parcel_services',
                              [
                                  'default' => 0
                              ]);
        }
    }
}