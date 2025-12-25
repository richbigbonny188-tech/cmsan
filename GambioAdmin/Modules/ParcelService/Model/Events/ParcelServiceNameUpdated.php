<?php
/* --------------------------------------------------------------
   ParcelServiceNameUpdated.php 2021-05-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ParcelService\Model\Events;

use Gambio\Admin\Modules\ParcelService\Model\ValueObjects\ParcelServiceId;

/**
 * Class ParcelServiceNameUpdated
 *
 * @package Gambio\Admin\Modules\ParcelService\Model\Events
 * @codeCoverageIgnore
 */
class ParcelServiceNameUpdated
{
    /**
     * @var ParcelServiceId
     */
    private $id;
    
    /**
     * @var string
     */
    private $newName;
    
    
    /**
     * ParcelServiceNameUpdated constructor.
     *
     * @param ParcelServiceId $id
     * @param string          $newName
     */
    private function __construct(ParcelServiceId $id, string $newName)
    {
        $this->id      = $id;
        $this->newName = $newName;
    }
    
    
    /**
     * @param ParcelServiceId $id
     * @param string          $newName
     *
     * @return ParcelServiceNameUpdated
     */
    public static function create(ParcelServiceId $id, string $newName): ParcelServiceNameUpdated
    {
        return new self($id, $newName);
    }
    
    
    /**
     * @return ParcelServiceId
     */
    public function parcelServiceId(): ParcelServiceId
    {
        return $this->id;
    }
    
    
    /**
     * @return string
     */
    public function newName(): string
    {
        return $this->newName;
    }
}