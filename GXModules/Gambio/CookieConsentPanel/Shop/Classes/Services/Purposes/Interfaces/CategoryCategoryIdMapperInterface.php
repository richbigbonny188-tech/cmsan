<?php
/* --------------------------------------------------------------
  CategoryCategoryIdMapperInterface.php 2020-01-10
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2020 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\CookieConsentPanel\Services\Purposes\Interfaces;

use Gambio\CookieConsentPanel\Services\Purposes\Exceptions\CategoryDoesNotExistsException;
use Gambio\CookieConsentPanel\Services\Purposes\ValueObjects\LanguageCode;

/**
 * Interface CategoryCategoryIdMapperInterface
 * @package Gambio\CookieConsentPanel\Services\Purposes\Interfaces
 */
interface CategoryCategoryIdMapperInterface
{
    /**
     * @param int          $id
     *
     * @param LanguageCode $languageCode
     *
     * @return CategoryInterface
     */
    public function CategoryFromCategoryId(int $id, LanguageCode $languageCode): CategoryInterface;
    
    
    /**
     * @param LanguageCode $code
     *
     * @return CategoryInterface[]
     */
    public function allCategories(LanguageCode $code): array;
}