<?php
/* --------------------------------------------------------------
  ContentZoneData.php 2019-10-24
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\StyleEdit\Core\Components\ContentZone\Entities;

use Gambio\StyleEdit\Core\SingletonPrototype;
use InvalidArgumentException;
use Gambio\StyleEdit\Core\Language\Services\LanguageService;
use KeyValueCollection, stdClass;

/**
 * Class ContentZoneData
 */
class ContentZoneData extends KeyValueCollection
{
    /**
     * ContentZoneData constructor.
     *
     * @param stdClass[] $keyValueArray
     *
     * @throws \ReflectionException
     */
    public function __construct(array $keyValueArray)
    {
        if (count($keyValueArray)) {
            /** @var LanguageService $languageService */
            $languageService = SingletonPrototype::instance()->get(LanguageService::class);

            foreach ($keyValueArray as &$contentZone) {

                if (!is_a($contentZone, stdClass::class)) {

                    throw new InvalidArgumentException(self::class . ' can only hold ' . stdClass::class);
                }

                $contentZone = $this->getTranslatedTexts($contentZone, $languageService);

                /** @var ContentZoneOption $contentZone */
                $contentZone = ContentZoneOption::createFromJsonObject($contentZone);
                $contentZone->update();
            }
            unset($contentZone);
        }

        parent::__construct($keyValueArray);
    }


    /**
     * @param string $id
     *
     * @return ContentZoneOption
     */
    public function getContentZoneById(string $id): ContentZoneOption
    {
        foreach ($this->getArray() as $contentZone) {
            /** @var ContentZoneOption $contentZone */
            if ($contentZone->id() === $id) {

                return $contentZone;
            }
        }

        throw new InvalidArgumentException('Could not find a ContentZone with the id: ' . $id);
    }

    /**
     * @param $contentZone
     * @param LanguageService $languageService
     *
     * @return stdClass
     */
    protected function getTranslatedTexts($contentZone, LanguageService $languageService): stdClass
    {
        foreach ($contentZone->rows as &$row) {
            foreach ($row as $elementName => &$elementValue) {
                if (!empty($elementValue->labelId)) {
                    $elementValue->label = $languageService->translate($elementValue->labelId);
                }

                if (!empty($row->cols)) {
                    foreach ($row->cols as &$col) {
                        foreach ($col as $colElementName => &$colElementValue) {
                            if (is_object($colElementValue) && !empty($colElementValue->labelId)) {
                                $colElementValue->label = $languageService->translate($colElementValue->labelId);
                            }
                        }
                    }
                }
            }
        }

        return $contentZone;
    }
}