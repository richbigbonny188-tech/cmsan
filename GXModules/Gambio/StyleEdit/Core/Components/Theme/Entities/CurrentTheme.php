<?php
/*--------------------------------------------------------------------------------------------------
    CurrentTheme.php 2019-10-21
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2019 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */
namespace Gambio\StyleEdit\Core\Components\Theme\Entities;

use Gambio\StyleEdit\Core\Components\Theme\Entities\Interfaces\CurrentThemeInterface;

/**
 * Class CurrentTheme
 * @package Gambio\StyleEdit\Core\Components\Theme\Entities
 */
class CurrentTheme implements CurrentThemeInterface
{
    /**
     * @var string
     */
    protected $id;
    
    
    /**
     * @return string
     */
    public function id(): string
    {
        return $this->id;
    }
    
    
    /**
     * CurrentTheme constructor.
     *
     * @param $id
     */
    public function __construct($id)
    {
        $this->id = $id;
    }
}