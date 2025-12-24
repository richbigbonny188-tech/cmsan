<?php
/* --------------------------------------------------------------
   ApiMetaData.php 2022-08-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Api\Application\Responses;

/**
 * Class ApiMetaData
 *
 * @package Gambio\Api\Application\Responses
 */
class ApiMetaData implements Interfaces\ApiMetaData
{
    /**
     * @var string[]
     */
    private $links;
    
    
    /**
     * ApiMetaData constructor.
     *
     * @param array $links
     */
    private function __construct(array $links)
    {
        $this->links = $links;
    }
    
    
    /**
     * @param array $links
     *
     * @return ApiMetaData
     */
    public static function create(array $links = []): ApiMetaData
    {
        return new self ($links);
    }
    
    
    /**
     * @inheritDoc
     */
    public function setLink(string $name, string $link): Interfaces\ApiMetaData
    {
        $this->links[$name] = $link;
        
        return $this;
    }
    
    
    /**
     * @inheritDoc
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            'links' => $this->links,
        ];
    }
}