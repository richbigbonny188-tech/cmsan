<?php
/*--------------------------------------------------------------
   AbstractWithdrawalTextHashes.php 2020-08-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\SetupWizard\Steps\LegalText\ValueObjects;

/**
 * Class AbstractWithdrawalTextHashes
 * @package Gambio\Admin\Modules\SetupWizard\Steps\LegalText\ValueObjects
 */
abstract class AbstractWithdrawalTextHashes
{
    /**
     * @var string
     */
    protected $heading;
    
    /**
     * @var string
     */
    protected $text;
    
    
    /**
     * AbstractWithdrawalTextHashes constructor.
     *
     * @param string $heading
     * @param string $text
     */
    public function __construct(
        string $heading,
        string $text
    ) {
        $this->heading = $heading;
        $this->text    = $text;
    }
    
    
    /**
     * @return string
     */
    public function heading(): string
    {
        return $this->heading;
    }
    
    
    /**
     * @return string
     */
    public function text(): string
    {
        return $this->text;
    }
}