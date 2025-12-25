<?php
/* --------------------------------------------------------------
   ParcelServiceDescriptions.php 2020-08-28
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ParcelService\Model\Collections;

use ArrayIterator;
use Gambio\Admin\Modules\ParcelService\Model\ParcelService;
use Gambio\Admin\Modules\ParcelService\Model\ValueObjects\ParcelServiceDescription;
use IteratorAggregate;
use Traversable;

/**
 * Class ParcelServiceDescriptions
 *
 * @package Gambio\Admin\Modules\ParcelService\Model\Collections
 */
class ParcelServiceDescriptions implements IteratorAggregate
{
    /**
     * @var ParcelServiceDescription[]
     */
    private $parcelServiceDescriptions;
    
    
    /**
     * ParcelServiceDescriptions constructor.
     *
     * @param ParcelServiceDescription[] $parcelServiceDescriptions
     */
    private function __construct(array $parcelServiceDescriptions)
    {
        $this->parcelServiceDescriptions = [];
        foreach ($parcelServiceDescriptions as $parcelServiceDescription) {
            $this->parcelServiceDescriptions[$parcelServiceDescription->languageCode()] = $parcelServiceDescription;
        }
    }
    
    
    /**
     * @param ParcelServiceDescription ...$parcelServiceDescriptions
     *
     * @return ParcelServiceDescriptions
     */
    public static function create(ParcelServiceDescription ...$parcelServiceDescriptions): ParcelServiceDescriptions
    {
        return new self($parcelServiceDescriptions);
    }
    
    
    /**
     * @return Traversable|ParcelService[]
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->parcelServiceDescriptions);
    }
    
    
    /**
     * @return array
     */
    public function toArray(): array
    {
        return array_map(static function (ParcelServiceDescription $parcelServiceDescription): array {
            return $parcelServiceDescription->toArray();
        },
            array_values($this->parcelServiceDescriptions));
    }
    
    
    /**
     * @param string $languageCode
     *
     * @return string
     */
    public function url(string $languageCode): string
    {
        return $this->parcelServiceDescriptions[$languageCode]->url() ?? '';
    }
    
    
    /**
     * @param string $languageCode
     *
     * @return string
     */
    public function comment(string $languageCode): string
    {
        return $this->parcelServiceDescriptions[$languageCode]->comment() ?? '';
    }
    
    
    /**
     * @param ParcelServiceDescription $newDescription
     */
    public function changeDescription(ParcelServiceDescription $newDescription): void
    {
        $this->parcelServiceDescriptions[$newDescription->languageCode()] = $newDescription;
    }
}