<?php
/* --------------------------------------------------------------
   FileManagerInterface.inc.php 2016-08-03
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface FileManagerInterface
 */
interface FileManagerInterface
{
	public function check_chmod();
	public function chmod_444($p_dir);
	public function chmod_777($p_dir);
	public function find_shop_dir($p_dir);
	public function get_directories($p_dir, $p_no_parent_dirs = false);
	public function getError();
	public function is_shop($p_dir);
	public function quit();
	public function write_robots_file($p_dir, $p_relative_shop_path, $p_https_server = false);
}