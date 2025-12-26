<?php
/*--------------------------------------------------------------------------------------------------
    ContentManagerPagesServiceInterface.php 2021-07-19
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2021 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

declare(strict_types=1);


namespace GXModules\Gambio\StyleEdit\Core\Components\ContentManager\Interfaces;

use ContentInterface;

/**
 * Interface ContentManagerPagesServiceInterface
 *
 * @package GXModules\Gambio\StyleEdit\Core\Components\ContentManager\Interfaces
 */
interface ContentManagerPagesServiceInterface
{
    
    /**
     * @param string $position
     *
     * @return \PagePosition
     */
    public function createPagePositionFromString(string $position): \PagePosition;
    
    
    /**
     * @param $contentManagerContent
     */
    public function createPage($contentManagerContent): void;
    
    
    /**
     * @param $contentManagerContent
     */
    public function updatePage($contentManagerContent): void;
    
    
    /**
     * @param \ContentIdentificationInterface $identification
     */
    public function deletePage(\ContentIdentificationInterface $identification): void;
    
    
    /**
     * @param \ContentIdentificationInterface $identification
     *
     * @return ContentInterface
     */
    public function findPageById(\ContentIdentificationInterface $identification): ContentInterface;
}