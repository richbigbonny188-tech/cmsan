<?php
/* --------------------------------------------------------------
   TranslatedException.php 2019-10-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

namespace Gambio\StyleEdit\Core;

use Exception;
use Gambio\StyleEdit\Core\Language\Services\LanguageService;
use Throwable;

/**
 * Class TranslatedException
 * @package Gambio\StyleEdit\Core
 */
class TranslatedException extends Exception
{
    protected $httpStatusCode = 500;
    protected $errorMessages  = [
        'UPLOADED_THEME_INVALID'         => 'StyleEdit.exceptions.uploaded-theme-invalid',
        'DUPLICATED_THEME_ID'            => 'StyleEdit.exceptions.duplicated-theme-id',
        'INVALID_PARENT_THEME_ID'        => 'StyleEdit.exceptions.invalid-parent-theme-id',
        'INVALID_PROPERTY_NAME_MISSING'  => 'StyleEdit.exceptions.invalid-property-name-missing',
        'INVALID_PROPERTY_TYPE_MISSING'  => 'StyleEdit.exceptions.invalid-property-type-missing',
        'INVALID_PROPERTY_VALUE_MISSING' => 'StyleEdit.exceptions.invalid-property-value-missing',
        'THEME_WITHOUT_CONFIGURATION'    => 'StyleEdit.exceptions.theme-without-configuration',
        'DUPLICATED_CHILD_ID'            => 'StyleEdit.exceptions.duplicated-child-id',
        'INVALID_COMPONENT_ID'           => 'StyleEdit.exceptions.invalid-component-id',
        'INVALID_THEME_ID'               => 'StyleEdit.exceptions.invalid-theme-id',
        'INVALID_COMPONENT'              => 'StyleEdit.exceptions.invalid-component',
        'COMPONENT_TYPE_NOT_SUPPLIED'    => 'StyleEdit.exceptions.component-type-not-supplied',
        'NOT_REGISTERED_CLASS'           => 'StyleEdit.exceptions.not-registered-class',
        'THEME_UPLOAD_MISSING_FILE'      => 'StyleEdit.exceptions.theme-upload-missing-file',
        'INVALID_FILE_TYPE'              => 'StyleEdit.exceptions.invalid-file-type',
        'INVALID_FILE_CONTENT'           => 'StyleEdit.exceptions.invalid-file-content',
        'FILE_CANT_BE_READ'              => 'StyleEdit.exceptions.file-cant-be-read',
        'INSUFFICIENT_PERMISSIONS'       => 'StyleEdit.exceptions.insufficient-permissions',
        'UNAUTHORIZED'                   => 'StyleEdit.exceptions.unauthorized'
    ];
    
    
    /**
     * @param $status
     *
     * @return $this
     */
    public function withHttpStatusCode($status) : self
    {
        $this->httpStatusCode = $status;
        
        return $this;
    }
    
    
    /**
     * @return int
     */
    public function httpStatusCode() : int
    {
        return $this->httpStatusCode;
    }
    
    
    /**
     * TranslatedException constructor.
     *
     * @param string          $identifier
     * @param array           $params
     * @param int             $code
     * @param \Throwable|null $previous
     *
     * @throws \Exception
     */
    public function __construct(
        $identifier = '',
        array $params = [],
        $httpStatusCode = 0,
        Throwable $previous = null,
        $code = 0
    ) {
        $this->httpStatusCode = $httpStatusCode;
        /**
         * @var LanguageService $languageService
         */
        $languageService = SingletonPrototype::instance()->get(LanguageService::class);
        
        if (array_key_exists($identifier, $this->errorMessages)) {
            $phraseId = $this->errorMessages[$identifier];
        } else {
            $phraseId = $identifier;
        }
        
        if (!$translated = $languageService->translate($phraseId)) {
            $translated = $phraseId;
        }
        $message = vsprintf($translated, $params);
        
        parent::__construct($message, $code, $previous);
    }
}