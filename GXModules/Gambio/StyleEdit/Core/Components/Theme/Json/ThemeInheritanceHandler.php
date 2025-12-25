<?php
/*--------------------------------------------------------------------------------------------------
    ThemeInheritanceHandlerFactory.php 2019-10-11
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2019 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\StyleEdit\Core\Components\Theme\Json;

use Gambio\StyleEdit\Core\Json\FileIO;
use Gambio\StyleEdit\Core\Json\InheritanceHandler;
use Gambio\StyleEdit\Core\Json\Interfaces\InheritanceResolverInterface;

/**
 * Class ThemeInheritanceHandlerFactory
 * @package Gambio\StyleEdit\Core\Components\Theme\Json
 */
class ThemeInheritanceHandler extends InheritanceHandler
{
    /**
     * ThemeInheritanceHandler constructor.
     *
     * @param FileIO                   $fileIO
     * @param ThemeInheritanceResolver $resolver
     *
     * @throws \Exception
     */
    public function __construct(FileIO $fileIO, ThemeInheritanceResolver $resolver)
    {
        parent::__construct('', $fileIO, $resolver);
    }
    
    
    /**
     * @param $filename
     *
     * @return void
     * @throws \Exception
     */
    public function setFilename($filename):void
    {
        parent::setFilename($filename);
    }
    
    
    public function __clone()
    {
        $this->filename = null;
        $this->basePath = null;
        $this->object   = null;
        $this->fileIO   = $this->fileIO;
    }
}