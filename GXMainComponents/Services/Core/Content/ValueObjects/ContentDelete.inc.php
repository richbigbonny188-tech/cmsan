<?php
/*--------------------------------------------------------------------------------------------------
    ContentProtected.inc.php 2023-03-06
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2023 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

declare(strict_types=1);


class ContentDelete implements ContentDeleteInterface
{
    /**
     * @var bool
     */
    protected $deletable;
    
    
    public function __construct(bool $deletable = true)
    {
        $this->deletable = $deletable;
    }
    
    
    /**
     * @inheritDoc
     */
    public function isDeletable(): bool
    {
        return $this->deletable;
    }
}
