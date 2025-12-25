<?php
/*--------------------------------------------------------------------------------------------------
    BoxService.php 2019-09-12
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2019 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */
declare(strict_types=1);

namespace Gambio\StyleEdit\Core\Services;

/**
 * Class BoxService
 * @package Gambio\StyleEdit\Core\Services
 */
class MenuBoxService
{
    protected const FREE      = true;
    protected const ALLOCATED = false;
    /**
     * @var array
     */
    protected $boxData;
    protected $boxSpots = null;
    /**
     * @var SettingsService
     */
    protected $settingsService;
    
    
    /**
     * SettingsService constructor.
     *
     * @param SettingsService $settingsService
     *
     * @throws \Exception
     */
    public function __construct(SettingsService $settingsService)
    {
        $this->settingsService = $settingsService;
        $this->init();
    }
    
    
    /**
     * @throws \Exception
     */
    protected function init(): void
    {
        if ($this->boxSpots === null) {
            $this->boxSpots = [
                self::FREE,
                self::FREE,
                self::FREE,
                self::FREE,
                self::FREE,
                self::FREE,
                self::FREE,
                self::FREE,
                self::FREE,
                self::FREE,
                self::FREE,
                self::FREE,
                self::FREE,
                self::FREE,
                self::FREE,
                self::FREE,
                self::FREE,
                self::FREE,
                self::FREE,
                self::FREE,
                self::FREE,
                self::FREE,
                self::FREE,
                self::FREE,
                self::FREE
            ];
            $this->boxData  = [];
            $boxGroup       = $this->settingsService->getScssByGroup('box');
            
            foreach ($boxGroup as $id => $value) {
                $this->setStatus($id, $value->status);
                $this->setPosition($id, $value->position);
            }
        }
    }
    
    
    /**
     * @param $boxName
     * @param $value
     */
    protected function setStatus(string $boxName, bool $value): void
    {
        if (isset($this->boxData[$boxName])) {
            $this->boxData[$boxName]['status'] = $value;
        } else {
            $this->boxData[$boxName] = ['status' => $value, 'position' => -1];
        }
    }
    
    
    /**
     * @param $boxName
     * @param $value
     */
    protected function setPosition(string $boxName, int $value): void
    {
        if (isset($this->boxData[$boxName])) {
            $this->boxData[$boxName]['position'] = $value;
        } else {
            $this->boxData[$boxName] = ['status' => true, 'position' => $value];
        }

        $this->boxSpots[$value] = self::ALLOCATED;
    }
    
    
    /**
     * Adapter Method
     *
     * @param string $boxName
     *
     * @return bool
     * @throws \Exception
     */
    public function getStatus(string $boxName)
    {
        
        if (!isset($this->boxData[$boxName])) {
            $this->setStatus($boxName, true);
        }
        
        return $this->boxData[$boxName]['status'];
    }
    
    
    /**
     * Adapter Method
     *
     * @param string $boxName
     *
     * @return string
     * @throws \Exception
     */
    public function getPosition(string $boxName): string
    {
        
        if (!isset($this->boxData[$boxName])
            || (isset($this->boxData[$boxName]) && $this->boxData[$boxName]['position'] < 0)) {
            $this->setPosition($boxName, $this->getFreeSpot());
        }
        
        //  This is temporary due to no implementations of Honeygrid specific options in StyleEdit4
        $result = 'gm_box_pos_' . ($this->boxData[$boxName]['position'] + 1);
        
        return $result;
    }
    
    
    /**
     * @return int
     */
    protected function getFreeSpot(): int
    {
        foreach ($this->boxSpots as $index => $value) {
            if ($value === self::FREE) {
                $this->boxSpots[$index] = self::ALLOCATED;
                
                return $index + 1;
            }
        }
        $this->boxSpots[] = self::ALLOCATED;
        
        return count($this->boxSpots);
    }
    
    
    /**
     * @param int $position
     *
     * @return bool
     * @throws \Exception
     */
    public function getStatusByPosition(int $position): bool
    {
        
        
        if (count($this->boxData) >= $position + 1) {
            foreach ($this->boxData as $box) {
                if (isset($box['position']) && $box['position'] === $position) {
                    return $box['status'];
                }
            }
        }
        
        return false;
    }
    
    
}