<?php
/* --------------------------------------------------------------
  StyleEditServiceFactory.inc.php 2021-05-04
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2021 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/
MainFactory::load_class(StyleEditServiceInterface::class);
MainFactory::load_class(StyleEdit4Service::class);

/**
 * Class StyleEditServiceFactory
 *
 */
class StyleEditServiceFactory
{
    /**
     * @var StyleEditServiceInterface
     */
    protected static $service;
    
    
    /**
     * @return StyleEditServiceInterface
     */
    public static function service(): StyleEditServiceInterface
    {
        if (self::$service === null) {
            self::$service = MainFactory::create(StyleEdit4Service::class);
        }
        
        return self::$service;
    }
}
