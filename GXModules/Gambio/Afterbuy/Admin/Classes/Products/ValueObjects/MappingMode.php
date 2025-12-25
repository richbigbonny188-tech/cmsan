<?php
/* --------------------------------------------------------------
   MappingMode.php 2023-10-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);

namespace GXModules\Gambio\Afterbuy\Admin\Classes\Products\ValueObjects;

/**
 * Class MappingMode
 *
 * @package GXModules\Gambio\Afterbuy\Admin\Classes\Products\ValueObjects
 */
class MappingMode
{
    
    public const PID_EAN = 'pid_ean';
    
    public const PID_ANR = 'pid_anr';
    
    public const MODEL_EAN = 'model_ean';
    
    public const MODEL_ANR = 'model_anr';
    
    public const MODEL_PRODID = 'model_prodid';
    
    /**
     * @var string
     */
    private string $mode;
    
    
    /**
     * @param string $mode
     */
    public function __construct(string $mode = 'pid_ean')
    {
        if (!defined('self::' . strtoupper($mode))) {
            throw new \RuntimeException('invalid mapping mode');
        }
        $this->mode = $mode;
    }
    
    
    /**
     * @return string
     */
    public function getMode(): string
    {
        return $this->mode;
    }
    
    
    /**
     * @param string $mode
     *
     * @return bool
     */
    public function is(string $mode): bool
    {
        return $this->mode === $mode;
    }
}
