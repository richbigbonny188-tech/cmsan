<?php

/* --------------------------------------------------------------
   DirectHelpForumLinkFactory.inc.php 2018-09-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class representing the forum link factory
 */
class DirectHelpForumLinkFactory
{
    /**
     * Forum root location
     *
     * @var string
     */
    protected $forumLocation;
    
    /**
     * Minimum random number
     *
     * @var int
     */
    protected $minRandomNumber;
    
    /**
     * Maximum random number
     *
     * @var int
     */
    protected $maxRandomNumber;
    
    
    /**
     * Create instance
     *
     * @param NonEmptyStringType $forumLocation   Forum location
     * @param IntType            $minRandomNumber Minimum random number
     * @param IntType            $maxRandomNumber Maximum random number
     */
    public function __construct(
        NonEmptyStringType $forumLocation,
        IntType $minRandomNumber,
        IntType $maxRandomNumber
    ) {
        $this->forumLocation   = $forumLocation->asString();
        $this->minRandomNumber = $minRandomNumber->asInt();
        $this->maxRandomNumber = $maxRandomNumber->asInt();
    }
    
    
    /**
     * Return the link to root page
     *
     * @return string
     */
    public function linkToRootPage()
    {
        return $this->forumLocation;
    }
    
    
    /**
     * Return the link to the forum search page with provided search term
     *
     * @param NonEmptyStringType $term Search term
     *
     * @return string
     */
    public function linkBySearchTerm(NonEmptyStringType $term)
    {
        return $this->forumLocation . '/search/' . $this->randomNumber() . '?q=' . $term->asString();
    }
    
    
    /**
     * Return random number
     *
     * @return string
     */
    protected function randomNumber()
    {
        return (string)mt_rand($this->minRandomNumber, $this->maxRandomNumber);
    }
}