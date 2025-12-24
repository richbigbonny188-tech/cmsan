<?php
/*
 * --------------------------------------------------------------
 *   GambioOmnibusPolicyTextPhrasesStorage.inc.php 2022-05-24
 *   Gambio GmbH
 *   http://www.gambio.de
 *   Copyright (c) 2022 Gambio GmbH
 *   Released under the GNU General Public License (Version 2)
 *   [http://www.gnu.org/licenses/gpl-2.0.html]
 * --------------------------------------------------------------
 */

/**
 * Class GambioOmnibusPolicyTextPhrasesStorage
 */
class GambioOmnibusPolicyTextPhrasesStorage extends ConfigurationStorage
{
    /**
     * Configuration storage namespace
     */
    const STORAGE_NAMESPACE = 'modules/gambio/omnibus_policy/reviews/phrases';
    /**
     * Language specific phrase storage
     *
     * @var array
     */
    private $textPhrasesKeys = [];


    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct(self::STORAGE_NAMESPACE);

        $this->textPhrasesKeys = [
            'content_verified_text',
            'content_verified_text_short',
        ];
    }


    /**
     * Return a phrase value
     *
     * @param string $key phrase key
     *
     * @return string phrase value
     * @throws InvalidArgumentException Invalid key
     */
    public function get($key)
    {
        $this->isAllowed($key);

        return parent::get($key);
    }


    /**
     * Return all phrase key/value pairs
     *
     * @return array Key/Value pairs
     */
    public function getAll()
    {
        return parent::get_all();
    }


    /**
     * Set a phrase value
     *
     * @param string $key   Configuration key
     * @param string $value Configuration value
     *
     * @return $this Same instance
     * @throws InvalidArgumentException Invalid key
     */
    public function set($key, $value)
    {
        $this->isAllowed($key);

        parent::set($key, $value);

        return $this;
    }


    /**
     * Set multiple text phrases at one time for specific key
     * Note: input data should one of two formats
     * 1) ['text_phrase_key_language_code_1' => 'text_value_1','text_phrase_key_language_code_2' => 'text_value_2']
     * 2) ['text_phrase_key' => ['language_code' => 'text_value']]
     *
     * @param array $data
     *
     * @return $this
     */
    public function setAll($data = [])
    {
        $parsedInput = [];

        foreach ($data as $key => $phrases) {
            $this->isAllowed($key);
            if (!is_array($phrases)) {
                // parse as key => $value
                $parsedInput[$key] = $phrases;
                continue;
            }
            /**
             * @param string $locale
             *
             * @return string
             */
            $getPhraseKey = function ($locale) use ($key) {
                return $key . '_' . strtolower($locale);
            };
            foreach ($phrases as $locale => $phrase) {
                $parsedInput[$getPhraseKey($locale)] = $phrase;
            }
        }

        // remove input with empty values
        $parsedInput = array_filter($parsedInput, function ($value) {
            return !empty($value);
        });

        parent::set_all($parsedInput);

        return $this;
    }


    /**
     * Removes a phrase value
     *
     * @param string $key Configuration key
     *
     * @return $this Same instance
     * @throws InvalidArgumentException Invalid key
     */
    public function delete($key)
    {
        $this->isAllowed($key);

        parent::delete($key);

        return $this;
    }


    /**
     * Removes a phrase value
     *
     * @return $this Same instance
     * @throws InvalidArgumentException Invalid key
     */
    public function deleteAll()
    {
        parent::delete_all();

        return $this;
    }


    /**
     * @param string $key
     * @param bool   $isOptional when false, return will be surprised and replaced with exception
     *
     * @note $key is being trimmed and changed in memory for backward-compatibility reasons
     *
     * @return bool
     */
    private function isAllowed(&$key, $isOptional = false)
    {
        $key = trim($key, '/');

        $startsWith = static function ($prefix) use ($key) {
            return substr($key, 0, strlen($prefix)) === $prefix;
        };

        if (empty(array_filter($this->textPhrasesKeys, $startsWith))) {
            if ($isOptional) {
                return false;
            }
            throw new InvalidArgumentException(
                sprintf("Invalid text phrase key '%s'", $key)
            );
        }
        return true;
    }
}