<?php
/* --------------------------------------------------------------
  StyleEditReaderInterface.inc.php 2021-07-29
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2021 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

/**
 * Interface StyleEditReaderInterface
 */
interface StyleEditReaderInterface
{
    /**
     * Get Sass Code by group and style name
     *
     * @param string $p_group
     *
     * @return string
     */
    public function getScss($p_group): string;
    
    
    /**
     * Returns the custom styles from a JSON File
     *
     * @return string
     */
    public function getCustomStyles(): string;
    
    
    /**
     * Searches for a setting value identified by its name. If no result is found, null will be returned.
     *
     * @param $p_settingName
     *
     * @return mixed|null
     */
    public function findSettingValueByName($p_settingName);
    
    /**
     * @param mixed ...$settingsNames
     *
     * @return array
     */
    public function findSettingValuesByNames(...$settingsNames): array;
    
    
    /**
     * Returns error message
     *
     * @return string
     */
    public function getErrorMessage(): string;
    
    
    /**
     * Adapter Method
     *
     * @param string $p_boxName
     *
     * @return bool
     */
    public function get_status($p_boxName): bool;
    
    
    /**
     * Adapter Method
     *
     * @param string $p_boxName
     *
     * @return string
     */
    public function get_position($p_boxName): string;


    /**
     * Adapter Method
     *
     * @param int $position
     *
     * @return bool
     */
    public function get_status_by_position($position): bool;
}