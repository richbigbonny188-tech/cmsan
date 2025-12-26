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
    'exceptions.uploaded-theme-invalid'         => "Das hochgeladene Theme is nicht mit StyleEdit kompatible.",
    'exceptions.duplicated-theme-id'            => "Das Theme '%s' hat die selbe ID wie Theme '%s'!",
    'exceptions.invalid-parent-theme-id'        => "Das Theme '%s' verweist auf ein invalides Theme '%s'!",
    'exceptions.invalid-property-id-missing'    => 'Invalide PropertyConfiguration: Id fehlt!',
    'exceptions.invalid-property-name-missing'  => 'Invalide PropertyConfiguration: Name fehlt!',
    'exceptions.invalid-property-type-missing'  => 'Invalide PropertyConfiguration: Typ fehlt!',
    'exceptions.invalid-property-value-missing' => 'Invalide PropertyConfiguration: Wert fehlt!',
    'exceptions.theme-without-configuration'    => "Das Verzeichnis %s hat keine valide JSON Datei! Fehler: '%s'!",
    'exceptions.duplicated-child-id'            => "Die ID %s wird bereits von der Komponente vom Typ '%s' genutzt!",
    'exceptions.invalid-component-id'           => 'Invalide ID für Komponente %s',
    'exceptions.invalid-theme-id'               => 'Invalide Theme ID: %s',
    'exceptions.invalid-component'              => 'Invalide Komponente: %s',
    'exceptions.component-type-not-supplied'    => 'Komponententyp wurde nicht angegeben',
    'exceptions.not-registered-class'           => 'Es gibt keinen Prototyp für Klasse %s',
    'exceptions.theme-upload-missing-file'      => 'Fehlende Datei!',
    'exceptions.invalid-file-type'              => 'Invalider MIME Typ. Die Datei muss vom Typ %s sein!',
    'exceptions.invalid-file-content'           => 'Die Datei hat keinen validen %s Inhalt.',
    'exceptions.invalid-json-file'              => 'Die JSON-Datei "%s" ist nicht gültig.',
    'exceptions.file-cant-be-read'              => 'Datei konnte nicht gelesen werden!',
    'exceptions.insufficient-permissions'       => 'Die Datei %s konnte nicht beschrieben werden',
    'exceptions.unauthorized'                   => 'Du bist nicht berechtigt auf diese Seite zuzugreifen!',
    'exceptions.preview-already-exists'         => 'Es gibt bereits eine Theme Vorschau für %s!',
    'exceptions.cant-create-preview-of-preview' => 'Es ist nicht möglich eine Vorschau für ein Vorschautheme zu machen!',
    
    'contentZone.class.label'         => 'Klasse',
    'contentZone.border.label'        => 'Rahmen',
    'contentZone.background.label'    => 'Hintergrund',
    'contentZone.row.alignment.label' => 'Inhalt wrappen',
    
    'INSUFFICIENT_PERMISSIONS' => 'Das Verzeichnis %s verfügbt nicht über die benötigten Rechte. StyleEdit konnte deshalb nicht gestartet werden.'
];
