<?php
/*--------------------------------------------------------------------------------------------------
    StyleEdit.lang.inc.php 2022-06-21
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2022 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */
$t_language_text_section_content_array = [
    'exceptions.uploaded-theme-invalid'         => "The uploaded theme is not a valid StyleEdit theme",
    'exceptions.duplicated-theme-id'            => "The theme '%s' has the same id as theme '%s'!",
    'exceptions.invalid-parent-theme-id'        => "The theme '%s' references an invalid parent theme '%s'!",
    'exceptions.invalid-property-id-missing'    => 'Invalid PropertyConfiguration: id is missing!',
    'exceptions.invalid-property-name-missing'  => 'Invalid PropertyConfiguration: name is missing!',
    'exceptions.invalid-property-type-missing'  => 'Invalid PropertyConfiguration: type is missing!',
    'exceptions.invalid-property-value-missing' => 'Invalid PropertyConfiguration: value is missing!',
    'exceptions.theme-without-configuration'    => "The directory %s doesn't have a valid json file! Error: %s!",
    'exceptions.duplicated-child-id'            => "The id %s is already in use by a component of type '%s'!",
    'exceptions.invalid-component-id'           => 'Invalid id for Component %s',
    'exceptions.invalid-theme-id'               => 'Invalid theme id: %s',
    'exceptions.invalid-component'              => 'Invalid component: %s',
    'exceptions.component-type-not-supplied'    => 'Component type was not supplied',
    'exceptions.not-registered-class'           => 'There is no prototype for class %s',
    'exceptions.theme-upload-missing-file'      => 'Missing file!',
    'exceptions.invalid-file-type'              => 'Invalid MIME type. The file must be of type %s',
    'exceptions.invalid-file-content'           => 'The file doesn\'t have a valid %s content',
    'exceptions.invalid-json-file'              => 'The json file "%s" is not valid.',
    'exceptions.file-cant-be-read'              => 'File can not be read!',
    'exceptions.insufficient-permissions'       => 'Could not write to %s',
    'exceptions.unauthorized'                   => 'You are not authorized to access this page',
    'exceptions.preview-already-exists'         => 'There is already a preview theme for  %s',
    'exceptions.cant-create-preview-of-preview' => 'There is no possible to create a preview of a preview theme',
    
    'contentZone.class.label'         => 'Class',
    'contentZone.border.label'        => 'Border',
    'contentZone.background.label'    => 'Background',
    'contentZone.row.alignment.label' => 'Wrap content',
    
    'INSUFFICIENT_PERMISSIONS' => 'The Directory %s does not have the required permissions. StyleEdit could not be started.'
];
