<?php
/* --------------------------------------------------------------
   Type.php 2022-08-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Configuration\Model\Entities;

use JsonSerializable;
use Webmozart\Assert\Assert;

/**
 * Class Type
 *
 * @package Gambio\Admin\Modules\Configuration\Model\Entities
 */
class Type implements JsonSerializable
{
    /**
     * @var string
     */
    private $id;
    
    /**
     * @var array
     */
    private $params;
    
    
    /**
     * Type constructor.
     *
     * @param string $id
     * @param array  $params
     */
    private function __construct(string $id, array $params)
    {
        $this->id     = $id;
        $this->params = $params;
    }
    
    
    /**
     * @param string $id
     * @param array  $params
     *
     * @return Type
     */
    public static function create(string $id, array $params = []): Type
    {
        Assert::notWhitespaceOnly($id, 'ID can not be whitespace only.');
        
        return new self($id, $params);
    }
    
    
    /**
     * @inheritDoc
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            'id'     => $this->id,
            'params' => $this->params,
        ];
    }
}