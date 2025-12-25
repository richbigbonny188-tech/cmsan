<?php
/* --------------------------------------------------------------
   Category.php 2022-08-05
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
 * Class Category
 *
 * @package Gambio\Admin\Modules\Configuration\Model\Entities
 */
class Category implements JsonSerializable
{
    /**
     * @var string
     */
    private $id;
    
    /**
     * @var string
     */
    private $label;
    
    
    /**
     * Category constructor.
     *
     * @param string $id
     * @param string $label
     */
    private function __construct(string $id, string $label)
    {
        $this->id    = $id;
        $this->label = $label;
    }
    
    
    /**
     * @param string $id
     * @param string $label
     *
     * @return Category
     */
    public static function create(string $id, string $label): Category
    {
        Assert::notWhitespaceOnly($id, 'Provided ID can not be whitespace only.');
        Assert::notWhitespaceOnly($label, 'Provided label can not be whitespace only.');
        
        return new self($id, $label);
    }
    
    
    /**
     * @return string
     */
    public function id(): string
    {
        return $this->id;
    }
    
    
    /**
     * @return string
     */
    public function label(): string
    {
        return $this->label;
    }
    
    
    /**
     * @return array Returns an array, that matches the Category schema from "configuration.schema.json".
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            'id'    => $this->id,
            'label' => $this->label,
        ];
    }
}