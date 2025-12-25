<?php
/*--------------------------------------------------------------
   Zone.php 2022-07-21
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Country\Model\Entities;

use Gambio\Admin\Modules\Country\Model\ValueObjects\ZoneId;
use Webmozart\Assert\Assert;

/**
 * Class Zone
 *
 * @package Gambio\Admin\Modules\Country\Model\Entities
 */
class Zone
{
    private ZoneId $id;
    private string $code;
    private string $name;
    
    
    /**
     * @param ZoneId $id
     * @param string $code
     * @param string $name
     */
    private function __construct(
        ZoneId $id,
        string $code,
        string $name
    ) {
        $this->id   = $id;
        $this->code = $code;
        $this->name = $name;
    }
    
    
    /**
     * @param ZoneId $id
     * @param string $code
     * @param string $name
     *
     * @return Zone
     */
    public static function create(
        ZoneId $id,
        string $code,
        string $name
    ): Zone {
        
        Assert::stringNotEmpty($code, 'Expected code to be non-empty string.');
        Assert::stringNotEmpty($name, 'Expected name to be non-empty string.');
        
        return new self($id, $code, $name);
    }
    
    
    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id'   => $this->id(),
            'code' => $this->code(),
            'name' => $this->name(),
        ];
    }
    
    
    /**
     * @return int
     */
    public function id(): int
    {
        return $this->id->value();
    }
    
    
    /**
     * @return string
     */
    public function code(): string
    {
        return $this->code;
    }
    
    
    /**
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }
}