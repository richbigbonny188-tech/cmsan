<?php
/* --------------------------------------------------------------
   Group.php 2022-08-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Configuration\Model\Entities;

use Gambio\Admin\Modules\Configuration\Model\Collections\Configurations;
use Gambio\Admin\Modules\Configuration\Model\Collections\Links;
use JsonSerializable;
use Webmozart\Assert\Assert;

/**
 * Class Group
 *
 * @package Gambio\Admin\Modules\Configuration\Model\Entities
 */
class Group implements JsonSerializable
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
     * @var Configurations
     */
    private $configurations;
    
    /**
     * @var Links
     */
    private $links;
    
    
    /**
     * Group constructor.
     *
     * @param string         $id
     * @param string         $label
     * @param Configurations $configurations
     * @param Links          $links
     */
    private function __construct(string $id, string $label, Configurations $configurations, Links $links)
    {
        $this->id             = $id;
        $this->label          = $label;
        $this->configurations = $configurations;
        $this->links          = $links;
    }
    
    
    /**
     * @param string         $id
     * @param string         $label
     * @param Configurations $configurations
     * @param Links          $links
     *
     * @return Group
     */
    public static function create(string $id, string $label, Configurations $configurations, Links $links): Group
    {
        Assert::notWhitespaceOnly($id, 'Provided ID can not be whitespace only.');
        Assert::notWhitespaceOnly($label, 'Provided label can not be whitespace only.');
        
        return new self($id, $label, $configurations, $links);
    }
    
    
    /**
     * @return string[]
     */
    public function tags(): array
    {
        return $this->configurations->tags();
    }
    
    
    /**
     * @return array
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            'id'             => $this->id,
            'label'          => $this->label,
            'configurations' => $this->configurations,
            'tags'           => $this->configurations->tags(),
            'links'          => $this->links,
        ];
    }
}