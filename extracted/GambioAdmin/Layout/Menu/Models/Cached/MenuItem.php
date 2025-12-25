<?php
/* --------------------------------------------------------------
 MenuItem.php 2020-03-02
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Layout\Menu\Models\Cached;

/**
 * Class MenuItem
 * @package Gambio\Admin\Layout\Menu\Models\Cached
 */
class MenuItem
{
    /**
     * @var string
     */
    private $id;
    
    /**
     * @var string
     */
    private $title;
    
    /**
     * @var string
     */
    private $url;
    
    /**
     * @var bool
     */
    private $isActive;
    
    
    /**
     * MenuItem constructor.
     *
     * @param string $title
     * @param string $url
     * @param bool   $isActive
     */
    public function __construct(string $title, string $url, bool $isActive)
    {
        $this->title    = $title;
        $this->url      = $url;
        $this->isActive = $isActive;
        $this->id       = 'id_' . md5($this->url);
    }
    
    
    /**
     * @return string
     */
    public function id(): string
    {
        return $this->id;
    }
    
    
    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->isActive;
    }
    
    
    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'title'    => $this->title,
            'class'    => '',
            'id'       => $this->id,
            'link'     => $this->url,
            'isActive' => $this->isActive,
        ];
    }
}