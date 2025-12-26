<?php
/* --------------------------------------------------------------
   ParcelServiceDetails.php 2021-10-08
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\TrackingCode\Model\ValueObjects;

use Webmozart\Assert\Assert;

/**
 * Class ParcelServiceDetails
 *
 * @package Gambio\Admin\Modules\TrackingCode\Model\ValueObjects
 */
class ParcelServiceDetails
{
    /**
     * @var int
     */
    private $parcelServiceId;
    
    /**
     * @var string
     */
    private $languageCode;
    
    /**
     * @var string
     */
    private $name;
    
    /**
     * @var string
     */
    private $url;
    
    /**
     * @var string
     */
    private $comment;
    
    /**
     * @var string
     */
    private $shipmentType;
    
    
    /**
     * ParcelServiceDetails constructor.
     *
     * @param int    $parcelServiceId
     * @param string $languageCode
     * @param string $name
     * @param string $url
     * @param string $comment
     * @param string $shipmentType
     */
    private function __construct(
        int    $parcelServiceId,
        string $languageCode,
        string $name,
        string $url,
        string $comment,
        string $shipmentType
    ) {
        $this->parcelServiceId = $parcelServiceId;
        $this->languageCode    = $languageCode;
        $this->name            = $name;
        $this->url             = $url;
        $this->comment         = $comment;
        $this->shipmentType    = $shipmentType;
    }
    
    
    /**
     * @param int    $parcelServiceId
     * @param string $languageCode
     * @param string $name
     * @param string $url
     * @param string $comment
     * @param string $shipmentType
     *
     * @return ParcelServiceDetails
     */
    public static function create(
        int    $parcelServiceId,
        string $languageCode,
        string $name,
        string $url = '',
        string $comment = '',
        string $shipmentType = ''
    ): ParcelServiceDetails {
        Assert::greaterThan($parcelServiceId, 0, 'Invalid parcel service ID provided. Got: %s');
        Assert::regex($languageCode, '/^[a-zA-Z]{2}$/', 'Given language code must be a two-digit ISO code. Got: %s');
        Assert::notWhitespaceOnly($name, 'Parcel service name can not be empty.');
        
        return new self($parcelServiceId, strtolower($languageCode), $name, $url, $comment, $shipmentType);
    }
    
    
    /**
     * @return int
     */
    public function parcelServiceId(): int
    {
        return $this->parcelServiceId;
    }
    
    
    /**
     * @return string
     */
    public function languageCode(): string
    {
        return $this->languageCode;
    }
    
    
    /**
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }
    
    
    /**
     * @return string
     */
    public function url(): string
    {
        return $this->url;
    }
    
    
    /**
     * @return string
     */
    public function comment(): string
    {
        return $this->comment;
    }
    
    
    /**
     * @return string
     */
    public function shipmentType(): string
    {
        return $this->shipmentType;
    }
    
    
    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id'           => $this->parcelServiceId(),
            'languageCode' => $this->languageCode(),
            'name'         => $this->name(),
            'url'          => $this->url(),
            'comment'      => $this->comment(),
            'shipmentType' => $this->shipmentType(),
        ];
    }
}