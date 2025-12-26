<?php

/* --------------------------------------------------------------
   LanguageProviderInterface.inc.php 2016-05-31
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface LanguageProviderInterface
 *
 * @category   System
 * @package    Shared
 * @subpackage Interfaces
 */
interface LanguageProviderInterface
{
    /**
     * Returns the language IDs.
     *
     * @return IdCollection
     * @throws InvalidArgumentException If ID is not valid.
     *
     * @throws UnexpectedValueException If no ID has been found.
     */
    public function getIds();
    
    
    /**
     * Returns the language codes.
     *
     * @return KeyValueCollection
     * @throws InvalidArgumentException If code is not valid.
     *
     * @throws UnexpectedValueException If no code has been found.
     */
    public function getCodes();
    
    
    /**
     * Returns the language code from a specific language, selected by the language ID.
     *
     * @param IdType $id Language ID.
     *
     * @return LanguageCode
     * @throws InvalidArgumentException If code is not valid.
     *
     * @throws UnexpectedValueException If no code has been found.
     */
    public function getCodeById(IdType $id);
    
    
    /**
     * Returns the directory from the a specific language, selected by the language ID.
     *
     * @param IdType $id Language ID.
     *
     * @return string
     * @throws InvalidArgumentException If code is not valid.
     *
     * @throws UnexpectedValueException If no directory has been found.
     */
    public function getDirectoryById(IdType $id);
    
    
    /**
     * Returns the charset from the a specific language, selected by the language ID.
     *
     * @param IdType $id Language ID.
     *
     * @return string
     * @throws UnexpectedValueException If no charset has been found.
     *
     */
    public function getCharsetById(IdType $id);
    
    
    /**
     * Returns the ID from the a specific language, selected by the language code.
     *
     * @param LanguageCode $code Language code.
     *
     * @return int
     * @throws UnexpectedValueException If no ID has been found.
     *
     */
    public function getIdByCode(LanguageCode $code);
    
    
    /**
     * Returns the directory from the a specific language, selected by the language code.
     *
     * @param LanguageCode $code Language code.
     *
     * @return string
     * @throws UnexpectedValueException If no directory has been found.
     *
     */
    public function getDirectoryByCode(LanguageCode $code);
    
    
    /**
     * Returns the charset from the a specific language, selected by the language code.
     *
     * @param LanguageCode $code Language code.
     *
     * @return string
     * @throws UnexpectedValueException If no directory has been found.
     *
     */
    public function getCharsetByCode(LanguageCode $code);
    
    
    /**
     * Returns the active language codes.
     *
     * @return KeyValueCollection
     * @throws InvalidArgumentException If code is not valid.
     *
     */
    public function getActiveCodes();
    
    
    /**
     * Returns the icon for a specific language by a given language code.
     *
     * @param LanguageCode $code The given language code
     *
     * @return string
     * @throws UnexpectedValueException If no icon has been found.
     *
     */
    public function getIconFilenameByCode(LanguageCode $code);
    
    
    /**
     * Returns the default language code.
     *
     * @return string
     * @throws InvalidArgumentException If no default code exists.
     *
     */
    public function getDefaultLanguageCode();
    
    
    /**
     * Returns the default language ID.
     *
     * @return int
     * @throws InvalidArgumentException If no default code exists.
     *
     */
    public function getDefaultLanguageId();
}