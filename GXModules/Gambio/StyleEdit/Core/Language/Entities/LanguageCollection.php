<?php
/*--------------------------------------------------------------------------------------------------
    LanguageCollection.php 2019-10-23
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2019 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\StyleEdit\Core\Language\Entities;

use KeyValueCollection;

/**
 * Class LanguageCollection
 * @package Gambio\StyleEdit\Core\Language\Entities
 */
class LanguageCollection extends KeyValueCollection
{
    /**
     * @param array $languages
     */
    public function __construct($languages = [])
    {
        foreach ($languages as $language) {
            $this->add($language);
        }
    }
    
    
    /**
     * Add a new module center module into the collection.
     *
     * @param Language $language
     */
    public function add(Language $language)
    {
        $type = $this->_getValidType();
        if (is_a($language, $type)) {
            $this->collectionContentArray[$language->code()] = $language;
        }
    }
    
    
    /**
     * Get valid item type.
     *
     * @return string
     */
    protected function _getValidType(): string
    {
        return Language::class;
    }
}