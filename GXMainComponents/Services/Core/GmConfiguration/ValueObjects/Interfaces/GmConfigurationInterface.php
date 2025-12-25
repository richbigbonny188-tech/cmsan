<?php
/* --------------------------------------------------------------
  GmConfigurationInterface.php 2019-08-13
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

/**
 * Interface GmConfigurationInterface
 */
interface GmConfigurationInterface
{
    /**
     * @return int
     */
    public function id(): int;
    
    
    /**
     * @return string
     */
    public function key(): string;
    
    
    /**
     * @return string
     */
    public function value(): string;
    
    
    /**
     * @return int
     */
    public function groupId(): int;
    
    
    /**
     * @return int
     */
    public function sortOrder(): int;
    
    
    /**
     * @param string $value
     */
    public function setValue(string $value): void;
    
    
    /**
     * @param int $groupId
     */
    public function setGroupId(int $groupId): void;
    
    
    /**
     * @param int $sortOrder
     */
    public function setSortOrder(int $sortOrder): void;
}