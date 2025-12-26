<?php
/* --------------------------------------------------------------
   set_memory_limit.php 2021-08-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

function set_memory_limit($limitInMegaBytes = 128)
{
	$minMemoryLimitGiven = false;
	$minMemoryLimit      = (string)$limitInMegaBytes . 'M';
	
	if(function_exists('ini_get') && function_exists('ini_set'))
	{
		$serverMemoryLimit = @ini_get('memory_limit');
		
		if(preg_match('/(^[\d]+)([KMG]?$)/', $serverMemoryLimit, $matches))
		{
			$memoryLimit = (int)$matches[1];
			if(isset($matches[2]) && $matches[2] === 'G')
			{
				$memoryLimit *= 1024;
			}
			elseif(isset($matches[2]) && $matches[2] === 'K')
			{
				$memoryLimit /= 1024;
			}
            elseif(isset($matches[2]) && $matches[2] === '')
            {
                $memoryLimit /= 1024 * 1024;
            }
			
			if($memoryLimit < $limitInMegaBytes)
			{
				@ini_set('memory_limit', $minMemoryLimit);
				if(@ini_get('memory_limit') === $minMemoryLimit)
				{
					$minMemoryLimitGiven = true;
				}
			}
			else
			{
				$minMemoryLimitGiven = true;
			}
		}
	}
	else
	{
		$minMemoryLimitGiven = true;
	}
	
	return $minMemoryLimitGiven;
}