<?php
/*--------------------------------------------------------------------------------------------------
    PreviewThemeSettings.php 2022-08-05
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2022 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\StyleEdit\Core\Components\Theme\Entities;

/**
 * Class PreviewThemeSettings
 * @package Gambio\StyleEdit\Core\Components\Theme\Entities
 */
class PreviewThemeSettings implements \JsonSerializable
{
    
    /**
     * @var string
     */
    protected $id;
    
    /**
     * @var String
     */
    protected $publishPath;
    
    /**
     * @var String
     */
    protected $compilePath;
    
    
    /**
     * @return String
     */
    public function compilePath(): String
    {
        return $this->compilePath;
    }
    
    
    /**
     * @return string
     */
    public function id(): string
    {
        return $this->id;
    }
    
    
    /**
     * @return String
     */
    public function publishPath(): String
    {
        return $this->publishPath;
    }
    
    
    /**
     * PreviewThemeSettings constructor.
     *
     * @param $id
     * @param $publishPath
     * @param $compilePath
     */
    public function __construct(string $id, string $publishPath, string $compilePath)
    {
        $this->id          = $id;
        $this->publishPath = $publishPath;
        $this->compilePath = $compilePath;
    }
    
    
    /**
     * Specify data which should be serialized to JSON
     * @link  https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return (Object)[
            'id'          => $this->id(),
            'publishPath' => $this->publishPath(),
            'compilePath' => $this->compilePath()
        ];
    }
}