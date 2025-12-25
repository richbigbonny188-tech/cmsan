<?php
/* --------------------------------------------------------------
   ParcelServices.php 2020-08-28
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
use IteratorAggregate;
use Traversable;

/**
 * Class ParcelServices
 *
 * @package Gambio\Admin\Modules\ParcelService\Model\Collections
 */
class ParcelServices implements IteratorAggregate
{
    /**
     * @var ParcelService[]
     */
    private $parcelServices;
    
    
    /**
     * ParcelServices constructor.
     *
     * @param array $parcelServices
     */
    private function __construct(array $parcelServices)
    {
        $this->parcelServices = $parcelServices;
    }
    
    
    /**
     * @param ParcelService ...$parcelServices
     *
     * @return ParcelServices
     */
    public static function create(ParcelService ...$parcelServices): ParcelServices
    {
        return new self($parcelServices);
    }
    
    
    /**
     * @return Traversable|ParcelService[]
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->parcelServices);
    }
    
    
    /**
     * @return array
     */
    public function toArray(): array
    {
        return array_map(static function (ParcelService $parcelService): array {
            return $parcelService->toArray();
        },
            $this->parcelServices);
    }
}