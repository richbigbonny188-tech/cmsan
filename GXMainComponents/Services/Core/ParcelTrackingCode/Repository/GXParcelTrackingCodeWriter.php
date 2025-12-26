<?php
/* --------------------------------------------------------------
 GXParcelTrackingCodeWriter.php 2018-01-11
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2018 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Class GXParcelTrackingCodeWriter
 */
class GXParcelTrackingCodeWriter
{
    /**
     * @var \CI_DB_query_builder
     */
    protected $db;
    
    /**
     * @var string
     */
    protected $table = 'orders_parcel_tracking_codes';
    
    
    /**
     * TrackingCodesWriter constructor.
     *
     * @param \CI_DB_query_builder $db
     */
    public function __construct(CI_DB_query_builder $db)
    {
        $this->db = $db;
    }
    
    
    /**
     * Saves the given order tracking code information in the order parcel tracking codes table.
     * Data for parcel service name, url and comment is fetched from related tables.
     *
     * @param \ParcelTrackingCodeServiceId  $parcelServiceId Id of parcel service.
     * @param \ParcelTrackingCodeOrderId    $orderId         Id of order.
     * @param \OrderParcelTrackingCode      $trackingCode    Tracking code for order.
     * @param \ParcelTrackingCodeLanguageId $languageId      Id of selected language.
     *
     * @return \ParcelTrackingCodeId Id of new order parcel tracking code.
     */
    public function save(
        ParcelTrackingCodeServiceId $parcelServiceId,
        ParcelTrackingCodeOrderId $orderId,
        OrderParcelTrackingCode $trackingCode,
        ParcelTrackingCodeLanguageId $languageId
    ) {
        
        $parcelServiceDescription = $this->db->select('ps.name, psd.url, psd.comment')
            ->from('parcel_services as ps')
            ->join('parcel_services_description as psd',
                   'ps.parcel_service_id = psd.parcel_service_id')
            ->where('ps.parcel_service_id', $parcelServiceId->is())
            ->where('psd.language_id', $languageId->is())
            ->get()
            ->row_array();
        
        if (!$parcelServiceDescription) {
            throw new InvalidArgumentException('Can not find related parcel service description for given parcel service id "'
                                               . $parcelServiceId->is() . '" and language id "' . $languageId->is()
                                               . '".');
        }
        
        $this->db->insert($this->table,
                          [
                              'order_id'            => $orderId->is(),
                              'tracking_code'       => $trackingCode->is(),
                              'parcel_service_id'   => $parcelServiceId->is(),
                              'language_id'         => $languageId->is(),
                              'parcel_service_name' => $parcelServiceDescription['name'],
                              'url'                 => str_replace('{TRACKING_NUMBER}',
                                                                   $trackingCode->is(),
                                                                   $parcelServiceDescription['url']),
                              'comment'             => $parcelServiceDescription['comment']
                          ]);
        
        return ParcelTrackingCodeId::create($this->db->insert_id());
    }
    
    
    /**
     * Saves the given order tracking code information in the order parcel tracking codes table.
     * Only the provided data is stored in the database.
     *
     * @param \ParcelTrackingCodeOrderId      $orderId Id of order.
     * @param \ParcelTrackingCodeServiceName  $name    Name of parcel service.
     * @param \ParcelTrackingCodeUrl          $url     Tracking code url for order.
     * @param \ParcelTrackingCodeComment|null $comment Comment about tracking code.
     *
     * @return \ParcelTrackingCodeId Id of new order parcel tracking code.
     */
    public function saveLightweight(
        ParcelTrackingCodeOrderId $orderId,
        ParcelTrackingCodeServiceName $name,
        ParcelTrackingCodeUrl $url,
        ParcelTrackingCodeComment $comment = null
    ) {
        $this->db->insert($this->table,
                          [
                              'order_id'            => $orderId->is(),
                              'parcel_service_name' => $name->is(),
                              'url'                 => $url->is(),
                              'comment'             => $comment ? $comment->is() : ''
                          ]);
        
        return ParcelTrackingCodeId::create($this->db->insert_id());
    }
}
