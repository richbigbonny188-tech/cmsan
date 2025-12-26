<?php
/* --------------------------------------------------------------
   TemplateDetailsMapper.php 2019-01-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

namespace Gambio\AdminFeed\Services\ShopInformation\Mapper;

use Gambio\AdminFeed\Services\ShopInformation\Reader\TemplateDetailsReader;
use Gambio\AdminFeed\Services\ShopInformation\ValueObjects\ThemeDetails;

/**
 * Class TemplateDetailsMapper
 *
 * @package Gambio\AdminFeed\Services\ShopInformation\Mapper
 */
class TemplateDetailsMapper
{
    /**
     * @var TemplateDetailsReader
     */
    private $reader;
    
    
    /**
     * TemplateDetailsMapper constructor.
     *
     * @param TemplateDetailsReader $reader
     */
    public function __construct(TemplateDetailsReader $reader)
    {
        $this->reader = $reader;
    }
    
    
    /**
     * Returns the template details.
     *
     * @return ThemeDetails
     */
    public function getTemplateDetails()
    {
        return new ThemeDetails($this->reader->getAvailableTemplates(),
                                $this->reader->getActiveTemplate(),
                                $this->reader->getActiveTemplateVersion());
    }
}