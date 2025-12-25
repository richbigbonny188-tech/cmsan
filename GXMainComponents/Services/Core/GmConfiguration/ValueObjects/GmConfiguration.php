<?php
/* --------------------------------------------------------------
  GmConfiguration.php 2019-08-13
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

/**
 * Class GmConfiguration
 */
class GmConfiguration implements GmConfigurationInterface
{
    /**
     * @var int
     */
    protected $id;
    
    /**
     * @var string
     */
    protected $key;
    
    /**
     * @var string
     */
    protected $value;
    
    /**
     * @var int
     */
    protected $groupId;
    
    /**
     * @var int
     */
    protected $sortOrder;
    
    
    /**
     * GmConfiguration constructor.
     *
     * @param int    $id
     * @param string $key
     * @param string $value
     * @param int    $groupId
     * @param int    $sortOrder
     */
    public function __construct(int $id, string $key, string $value, int $groupId, int $sortOrder)
    {
        $this->id        = $id;
        $this->key       = $key;
        $this->value     = $value;
        $this->groupId   = $groupId;
        $this->sortOrder = $sortOrder;
    }
    
    
    /**
     * @return int
     */
    public function id(): int
    {
        return $this->id;
    }
    
    
    /**
     * @return string
     */
    public function key(): string
    {
        return $this->key;
    }
    
    
    /**
     * @return string
     */
    public function value(): string
    {
        return $this->value;
    }
    
    
    /**
     * @return int
     */
    public function groupId(): int
    {
        return $this->groupId;
    }
    
    
    /**
     * @return int
     */
    public function sortOrder(): int
    {
        return $this->sortOrder;
    }
    
    
    /**
     * @param string $value
     */
    public function setValue(string $value): void
    {
        $this->value = $value;
    }
    
    
    /**
     * @param int $groupId
     */
    public function setGroupId(int $groupId): void
    {
        $this->groupId = $groupId;
    }
    
    
    /**
     * @param int $sortOrder
     */
    public function setSortOrder(int $sortOrder): void
    {
        $this->sortOrder = $sortOrder;
    }
}