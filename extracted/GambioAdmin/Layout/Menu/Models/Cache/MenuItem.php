<?php
/* --------------------------------------------------------------
 MenuItem.php 2020-01-30
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 30 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Layout\Menu\Models\Cache;

use Gambio\Admin\Layout\Menu\Filter\Conditions;
use Webmozart\Assert\Assert;

/**
 * Class MenuItem
 * @package Gambio\Admin\Layout\Menu\Models\Cache
 */
class MenuItem implements Sortable
{
    /**
     * @var string
     */
    private $title;
    
    /**
     * @var string
     */
    private $link;
    
    /**
     * @var string
     */
    private $linkParam;
    
    /**
     * @var int
     */
    private $sortOrder;
    
    /**
     * @var Conditions
     */
    private $conditions;
    
    
    /**
     * MenuItem constructor.
     *
     * @param string          $title
     * @param string          $link
     * @param int             $sortOrder
     * @param string|null     $linkParam
     * @param Conditions|null $conditions
     */
    private function __construct(
        string $title,
        string $link,
        int $sortOrder,
        ?string $linkParam,
        ?Conditions $conditions
    ) {
        $this->title      = $title;
        $this->link       = $link;
        $this->linkParam  = $linkParam;
        $this->sortOrder  = $sortOrder;
        $this->conditions = $conditions;
    }
    
    
    /**
     * Factory method for create MenuItem.
     *
     * @param array           $data
     * @param Conditions|null $condition
     *
     * @return static
     */
    public static function fromArray(array $data, Conditions $condition = null): self
    {
        Assert::keyExists($data, 'title');
        Assert::keyExists($data, 'link');
        Assert::keyExists($data, 'sort');
        
        return new static($data['title'], $data['link'], (int)$data['sort'], $data['link_param'] ?? null, $condition);
    }
    
    
    /**
     * @return array
     */
    public function toArray(): array
    {
        if ($this->linkParam) {
            return [
                'title'     => $this->title,
                'link'      => $this->link,
                'linkParam' => $this->linkParam,
                'class'     => ''
            ];
        }
        
        return [
            'title' => $this->title,
            'link'  => $this->link,
            'class' => ''
        ];
    }
    
    
    /**
     * Filter condition.
     *
     * @return Conditions|null
     */
    public function conditions(): ?Conditions
    {
        return $this->conditions;
    }
    
    
    /**
     * @return int
     */
    public function sortOrder(): int
    {
        return $this->sortOrder;
    }
}