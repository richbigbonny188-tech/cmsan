<?php
/*--------------------------------------------------------------------------------------------------
    CustomThemeCopyResponse.php 2019-06-06
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2016 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

/**
 * Class CustomThemeCopyResponse
 */
class CustomThemeCopyResponse implements CustomThemeCopyResponseInterface
{
    /**
     * @var ExistingFileCollection
     */
    protected $whitelist;
    
    /**
     * @var ExistingFileCollection
     */
    protected $overloaders;
    
    
    /**
     * @return ExistingFileCollection
     */
    public function getWhiteList()
    {
        return $this->whitelist;
    }
    
    
    /**
     * @return ExistingFileCollection
     */
    public function getOverloaders()
    {
        return $this->overloaders;
    }
    
    
    /**
     * CustomThemeCopyResponse constructor.
     *
     * @param ExistingFileCollection $overloaders
     * @param ExistingFileCollection $whitelist
     */
    public function __construct(ExistingFileCollection $overloaders, ExistingFileCollection $whitelist)
    {
        $this->overloaders = $overloaders;
        $this->whitelist   = $whitelist;
    }
    
    
    /**
     * @param CustomThemeCopyResponseInterface $response
     *
     * @return void
     */
    public function append(CustomThemeCopyResponseInterface $response = null)
    {
        if ($response) {
            $this->overloaders = new ExistingFileCollection(array_merge($this->overloaders->getArray(),
                                                                        $response->getOverloaders()->getArray()));
            $this->whitelist   = new ExistingFileCollection(array_merge($this->whitelist->getArray(),
                                                                        $response->getWhiteList()->getArray()));
        }
    }
    
    
    public function appendWhitelist(CustomThemeCopyResponseInterface $response = null)
    {
        if ($response) {
            $this->whitelist = new ExistingFileCollection(array_merge($this->whitelist->getArray(),
                                                                      $response->getWhiteList()->getArray()));
        }
    }
    
    
    /**
     * @param ExistingFileCollection $overloaders
     * @param ExistingFileCollection $whitelist
     *
     * @return bool|CustomThemeCopyResponse
     */
    public static function createWithOverloadsAndWhitelist(
        ExistingFileCollection $overloaders,
        ExistingFileCollection $whitelist
    ) {
        return MainFactory::create(static::class, $overloaders, $whitelist);
    }
    
    
    /**
     * @return bool|CustomThemeCopyResponse
     */
    public static function create()
    {
        $overloaders = new ExistingFileCollection([]);
        $whitelist   = new ExistingFileCollection([]);
        
        return MainFactory::create(static::class, $overloaders, $whitelist);
    }
}