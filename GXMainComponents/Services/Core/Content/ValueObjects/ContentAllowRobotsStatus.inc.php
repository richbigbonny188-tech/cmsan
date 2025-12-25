<?php

/* --------------------------------------------------------------
   ContentAllowRobotsStatus.inc.php 2022-05-02
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ContentAllowRobotsStatus
 *
 * This class represents the content "allow robots" status
 *
 * @category   System
 * @package    Content
 */
class ContentAllowRobotsStatus
{
    /**
     * Content "allow robots" status
     *
     * @var bool
     */
    protected $allowRobots;
    
    /**
     * Language code
     *
     * @var string
     */
    protected $languageCode;
    
    
    /**
     * ContentAllowRobotsStatus constructor
     *
     * @param bool              $allowRobots  Content "allow robots" status
     * @param LanguageCode|null $languageCode Language code
     */
    public function __construct(bool $allowRobots = true, LanguageCode $languageCode = null)
    {
        $this->allowRobots  = $allowRobots;
        $this->languageCode = $languageCode ? : new LanguageCode(new StringType('DE'));
    }
    
    
    /**
     * Return the content "allow robots" status
     *
     * @return bool
     */
    public function isAllowed(): bool
    {
        return $this->allowRobots;
    }
    
    
    /**
     * Return the language code
     *
     * @return string
     */
    public function languageCode(): string
    {
        return $this->languageCode;
    }
}