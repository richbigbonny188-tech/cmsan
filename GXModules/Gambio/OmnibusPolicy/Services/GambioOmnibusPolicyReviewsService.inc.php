<?php
/*
 * --------------------------------------------------------------
 *   GambioOmnibusPolicyReviewsService.inc.php 2022-05-25
 *   Gambio GmbH
 *   http://www.gambio.de
 *   Copyright (c) 2022 Gambio GmbH
 *   Released under the GNU General Public License (Version 2)
 *   [http://www.gnu.org/licenses/gpl-2.0.html]
 * --------------------------------------------------------------
 */

/**
 * Class GambioOmnibusPolicyReviewsService
 */
class GambioOmnibusPolicyReviewsService
{
    /**
     * @var GambioOmnibusPolicyConfigurationStorage
     */
    private $configuration;
    /**
     * @var GambioOmnibusPolicyTextPhrasesStorage
     */
    private $textPhrases;


    /**
     * @param string $key
     * @param string $configKey
     * @param string $languageCode
     *
     * @return string|null
     */
    public function getReviewsPolicy($key, $configKey, $languageCode)
    {
        $phrase = '';

        $policyIsVisible = $this->configuration()->get($configKey) == 1;

        if ($policyIsVisible) {
            $phraseKey = !$languageCode ? $key : implode('_', [$key, strtolower($languageCode)]);
            $phrase    = $this->textPhrases()->get($phraseKey);
        }

        return !empty($phrase) ? $phrase : null;
    }


    /**
     * checks if specific verification badge/text is enabled by key
     *
     * @param string $key
     *
     * @return bool
     */
    public function reviewsBadgeIsEnabled($key)
    {
        return $this->configuration()->get($key) == 1;
    }


    /**
     * @return GambioOmnibusPolicyConfigurationStorage
     */
    private function configuration()
    {
        if (!$this->configuration) {
            $this->configuration = MainFactory::create('GambioOmnibusPolicyConfigurationStorage');
        }

        return $this->configuration;
    }


    /**
     * @return GambioOmnibusPolicyTextPhrasesStorage
     */
    private function textPhrases()
    {
        if (!$this->textPhrases) {
            $this->textPhrases = MainFactory::create('GambioOmnibusPolicyTextPhrasesStorage');
        }

        return $this->textPhrases;
    }
}