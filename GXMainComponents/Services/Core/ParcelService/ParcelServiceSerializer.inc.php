<?php
/* --------------------------------------------------------------
   ParcelServiceSerializer.inc.php 2023-03-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ParcelServiceSerializer
 */
class ParcelServiceSerializer
{
    protected $languageProvider;
    
    
    public function __construct(LanguageProvider $languageProvider)
    {
        $this->languageProvider = $languageProvider;
    }
    
    
    /**
     * Serializes the given parcel service into an array.
     *
     * @param \ParcelServiceInterface $parcelService Parcel service to be serialized.
     *
     * @return array Serialized parcel service.
     */
    public function serialize(\ParcelServiceInterface $parcelService)
    {
        $urls     = [];
        $comments = [];
        
        foreach ($parcelService->parcelServiceDescriptions() as $description) {
            /** @var \ParcelServiceDescriptionInterface $description */
            $languageCode = $this->languageProvider->getCodeById(new IdType($description->languageId()))->asString();
            $languageCode = strtolower($languageCode);
            
            $urls[$languageCode]     = $description->url();
            $comments[$languageCode] = $description->comment();
        }
        
        if ($parcelService->id()) {
            return [
                'id'           => $parcelService->id(),
                'name'         => $parcelService->name(),
                'isDefault'    => $parcelService->isDefault(),
                'descriptions' => ['url' => $urls, 'comment' => $comments]
            ];
        }
        
        return [
            'name'         => $parcelService->name(),
            'isDefault'    => $parcelService->isDefault(),
            'descriptions' => ['url' => $urls, 'comment' => $comments]
        ];
    }
    
    
    /**
     * Encodes the given parcel service into a json string.
     *
     * @param \ParcelServiceInterface $parcelService Parcel service to be serialized.
     *
     * @return string Serialized  parcel service.
     */
    public function encode(\ParcelServiceInterface $parcelService)
    {
        return json_encode($this->serialize($parcelService));
    }
    
    
    /**
     * Deserialize the given json string to a parcel service entity.
     *
     * @param string|array $parcelService Parcel service.
     *
     * @return \GXParcelService Parcel service entity.
     */
    public function deserialize($parcelService)
    {
        $data         = is_string($parcelService) ? json_decode($parcelService, true) : $parcelService;
        $descriptions = [];
        $id           = ParcelServiceId::create(array_key_exists('id', $data) ? $data['id'] : null);
        
        foreach ($data['descriptions']['url'] as $languageCode => $url) {
            $languageId                 = $this->languageProvider->getIdByCode(new LanguageCode(new StringType($languageCode)));
            $parcelServiceDescriptionId = ParcelServiceDescriptionId::create($id, $languageId);
            $comment                    = $data['descriptions']['comment'][$languageCode];
            
            $descriptions[] = ParcelServiceDescription::create($parcelServiceDescriptionId, $url, $comment);
        }
        
        $parcelService = GXParcelService::create($id,
                                                 $data['name'],
                                                 $data['isDefault'],
                                                 ParcelServiceDescriptionCollection::collect($descriptions));
        
        return $parcelService;
    }
}