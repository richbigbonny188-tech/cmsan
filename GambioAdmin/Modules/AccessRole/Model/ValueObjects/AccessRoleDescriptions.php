<?php
/* --------------------------------------------------------------
   AccessRoleDescriptions.php 2021-09-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\AccessRole\Model\ValueObjects;

use Webmozart\Assert\Assert;

/**
 * Class AccessRoleDescriptions
 *
 * @package Gambio\Admin\Modules\AccessRole\Model\ValueObjects
 */
class AccessRoleDescriptions
{
    /**
     * @var array<string, string>
     */
    private $descriptions;
    
    
    /**
     * AccessRoleDescriptions constructor.
     */
    private function __construct()
    {
        $this->descriptions = [];
    }
    
    
    /**
     * @param array $descriptions
     *
     * @return AccessRoleDescriptions
     */
    public static function create(array $descriptions): AccessRoleDescriptions
    {
        $collection = new self();
        foreach ($descriptions as $languageCode => $description) {
            $collection->addDescription($languageCode, $description);
        }
        
        return $collection;
    }
    
    
    /**
     * @param string $languageCode
     *
     * @return string
     */
    public function getDescription(string $languageCode): string
    {
        return $this->descriptions[strtolower($languageCode)] ?? '';
    }
    
    
    /**
     * @param string $languageCode
     * @param string $description
     */
    public function addDescription(string $languageCode, string $description): void
    {
        Assert::regex($languageCode, '/^[a-z]{2}$/i', 'Given language code must be a two-digit ISO code.');
        
        $this->descriptions[strtolower($languageCode)] = $description;
    }
}