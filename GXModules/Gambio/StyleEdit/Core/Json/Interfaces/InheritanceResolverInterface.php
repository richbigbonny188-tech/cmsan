<?php
/*--------------------------------------------------------------------------------------------------
    InheritanceResolverInterface.php 2019-10-11
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2019 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\StyleEdit\Core\Json\Interfaces;

/**
 * Class ExtendsHandlerInterface
 * @package Gambio\StyleEdit\Core\Json
 */
interface InheritanceResolverInterface
{
    /**
     * @param $settings
     *
     * @param $sourceFilename
     *
     * @return string
     */
    public function resolveInheritanceFileName($settings, $sourceFilename) : string;
    
    
    /**
     * @param $settings
     *
     * @param $sourceFilename
     *
     * @return string
     */
    public function resolveInheritanceType($settings, $sourceFilename) : string;
    
    /**
     * @return string
     */
    public function jsonExtensionFolder(): string;

    /**
     * @param $settings
     * @param $resourcePath
     * @param $sourceFilename
     * @return mixed
     */
    public function getResourceRelativePath($settings, $resourcePath, $sourceFilename);
}