<?php

/* --------------------------------------------------------------
	ResponsiveFileManagerLanguageTextManager.inc.php 2017-09-28
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2017 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

/**
 * Class ResponsiveFileManagerLanguageTextManager
 *
 * Class representing the language text manager for the responsive file manager.
 */
class ResponsiveFileManagerLanguageTextManager
{
    /**
     * Language section.
     */
    const SECTION = 'responsivefilemanager';

    /**
     * Language text manager.
     *
     * @var LanguageTextManager
     */
    protected $languageTextManager;

    /**
     * ResponsiveFileManagerLanguageTextManager constructor.
     *
     * @param int $languageId Language ID.
     */
    public function __construct($languageId)
    {
        $this->languageTextManager = MainFactory::create('LanguageTextManager', self::SECTION, $languageId);
    }

    /**
     * Returns a phrase.
     *
     * @param string $phraseKey Phrase key.
     * @return string Phrase value.
     */
    public function getText($phraseKey)
    {
        return $this->languageTextManager->get_text($phraseKey);
    }
}