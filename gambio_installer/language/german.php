<?php
/* --------------------------------------------------------------
   german.php 2022-08-23
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------*/

# Button-Labels
define('BUTTON_BACK', 'Zur&uuml;ck');
define('BUTTON_CHECK_MISSING_FILES', 'Erneut &uuml;berpr&uuml;fen');
define('BUTTON_CHECK_PERMISSIONS', 'Rechte erneut &uuml;berpr&uuml;fen');
define('BUTTON_CONNECT', 'Verbinden');
define('BUTTON_CONNECT_NEW', 'Neu verbinden');
define('BUTTON_CONTINUE', 'Mit der Installation fortfahren');
define('BUTTON_DOWNLOAD', 'Download');
define('BUTTON_ENGLISH', 'English');
define('BUTTON_FINISH', 'Shopeinrichtung abschlie&szlig;en');
define('BUTTON_GAMBIO_PORTAL', 'Zum Gambio Kundenportal');
define('BUTTON_GERMAN', 'Deutsch');
define('BUTTON_OPEN_SHOP', 'Zum Shop');
define('BUTTON_SET_PERMISSIONS', 'Rechte setzen');
define('BUTTON_START', 'Shopeinrichtung starten');
define('BUTTON_SKIP', 'Installationsfortsetzung erzwingen');


# Headlines
define('HEADING_INSTALLATION_SERVICE', 'Gambio Installations-Service');
define('HEADING_INSTALLATION', 'Zur Installation');
define('HEADING_WRONG_PERMISSIONS', 'Folgende Dateien oder Ordner haben keine vollen Schreibrechte (777):');
define('HEADING_FTP_DATA', 'FTP-DATEN');
define('HEADING_REMOTE_CONSOLE', 'Remote-Konsole');
define('HEADING_DATABASE', 'Datenbankinformationen');
define('HEADING_SHOP_INFORMATION', 'Shopinformationen');
define('HEADING_ADMIN_DATA', 'Shopbetreiberdaten');
define('HEADLINE_ROBOTS', 'ROBOTS.TXT ANLEGEN');
define('HEADING_SUCCESS', 'Shopeinrichtung war erfolgreich');
define('HEADING_REGISTER_GLOBALS', 'Sicherheitsrisiko festgestellt');
define('HEADING_PROGRESS', 'Shopdatenbank wird eingerichtet');
define('HEADING_SYSTEM_REQUIREMENTS', 'Systemvoraussetzungen nicht erfüllt');


# Texts
define('TEXT_INSTALLATION_SERVICE', 'Du m&ouml;chtest die Installation nicht selbst durchf&uuml;hren? Nutze unseren Installations-Service!');
define('TEXT_INSTALLATION', 'W&auml;hle die gew&uuml;nschte Sprache f&uuml;r deine Installation.');
define('TEXT_SET_PERMISSIONS', 'Du kannst die Rechte entweder selbst mit einem FTP-Programm oder &uuml;ber die FTP-Funktion des Installers setzen.
F&uuml;r Letzteres gib bitte im folgenden Formular deine FTP-Daten ein und klicken auf &quot;Verbinden&quot;.<br />
Anschlie&szlig;end navigiere zum Verzeichnis, in dem sich der Shop befindet und starte die Rechtevergabe, indem du auf den Button &quot;Rechte setzen&quot; klickst.');
define('TEXT_ROBOTS','Klicke auf &quot;Download&quot;, um die robots.txt f&uuml;r deinen Shop zu generieren und herunterzuladen.
Lade die Datei mit einem FTP-Programm in das Haupt-Verzeichnis deines Webservers.
Die Datei muss anschlie&szlig;end unter folgendem Link erreichbar sein: <a href="http://' . getenv('HTTP_HOST') . '/robots.txt" target="_blank">http://' . getenv('HTTP_HOST') . '/robots.txt</a>');
define('TEXT_SUCCESS','Wir gratulieren dir zur Installation deines neuen Onlineshops und w&uuml;nschen dir viel Erfolg und gute Ums&auml;tze!<br /><br />Dein Gambio.de Service-Team.');
define('TEXT_FINAL_SETTINGS', 'Finale Einrichtung l&auml;uft...bitte warten.');
define('TEXT_WRITE_ROBOTS_FILE', 'robots.txt wird versucht automatisch anzulegen...bitte warten.');
define('TEXT_TABLES_EXIST', 'In der folgenden Auflistung rot markierte Tabellen werden im n&auml;chsten Schritt unwiderruflich gel&ouml;scht! Enthaltene Daten gehen verloren!');
define('TEXT_MISSING_FILES', 'Folgende Dateien oder Ordner fehlen. Lade diese mit einem FTP-Programm auf deinen Server und klicke anschlie&szlig;en auf &quot;Erneut &uuml;berpr&uuml;fen&quot;, um die Vollst&auml;ndigkeit sicherzustellen.');
define('TEXT_REGISTER_GLOBALS', '&quot;register_globals&quot; ist in der Konfiguration deines Shopservers aktiviert. Dies stellt ein Sicherheitsrisiko dar. Wir empfehlen dir dich an deinen Provider zu wenden, damit dieser &quot;register_globals&quot; f&uuml;r deinen Server deaktiviert.');
define('TEXT_PROGRESS', 'Dieser Vorgang kann mehrere Minuten dauern und sollte nicht abgebrochen werden.');
define('TEXT_SKIP', 'Du kannst die Installation fortsetzen, wenn du sicher bist, dass die Rechte bereits korrekt gesetzt sind und deren Erkennung aus technischen Gründen fehlschlägt.');
define('TEXT_SYSTEM_REQUIREMENTS', 'Die Systemvoraussetzungen für diese Shopversion sind leider nicht erfüllt, so dass der Shop nicht ordnungsgemäß funktionieren wird. Um das Problem zu lösen, wende dich bitte an deinen Hosting-Anbieter mit der Bitte die PHP-Erweiterung "intl" und "soap" zu installieren.');

# Form-Labels
define('LABEL_PROTOCOL', 'Protokoll');
define('LABEL_FTP', 'FTP');
define('LABEL_SFTP', 'SFTP');
define('LABEL_FTP_SERVER', 'FTP-Server');
define('LABEL_FTP_USER', 'FTP-Benutzer');
define('LABEL_FTP_PASSWORD', 'FTP-Passwort');
define('LABEL_FTP_PASV', 'passiv:');
define('LABEL_FTP_PORT', 'FTP-Port');
define('LABEL_DIR_UP', 'Verzeichnis nach oben');
define('LABEL_DB_SERVER', 'Server');
define('LABEL_DB_USER', 'Benutzer');
define('LABEL_DB_PASSWORD', 'Passwort');
define('LABEL_DB_DATABASE', 'Datenbank');
define('LABEL_HTTP_SERVER', 'Shop URL');
define('LABEL_SSL', 'SSL aktivieren');
define('LABEL_NOTICE', 'Hinweis:');
define('LABEL_HTTPS_SERVER', 'HTTPS-Server');
define('LABEL_GENDER', 'Anrede');
define('LABEL_MALE', 'Herr');
define('LABEL_FEMALE', 'Frau');
define('LABEL_OTHER', 'Keine');
define('LABEL_FIRSTNAME', 'Vorname');
define('LABEL_LASTNAME', 'Nachname');
define('LABEL_EMAIL', 'E-Mail');
define('LABEL_STREET', 'Stra&szlig;e');
define('LABEL_STREET_NUMBER', 'Hausnummer');
define('LABEL_POSTCODE', 'PLZ');
define('LABEL_CITY', 'Ort');
define('LABEL_STATE', 'Bundesland');
define('LABEL_COUNTRY', 'Land');
define('LABEL_TELEPHONE', 'Telefon');
define('LABEL_PASSWORD', 'Passwort');
define('LABEL_CONFIRMATION', 'Wiederholung');
define('LABEL_SHOP_NAME', 'Shopname');
define('LABEL_COMPANY', 'Firma');
define('LABEL_EMAIL_FROM', 'Absender-E-Mail');
define('LABEL_FORCE_DB', 'Trotzdem fortfahren!');
define('LABEL_VERSION_INFO_CONFIRMATION', 'Versions-Info zur Kenntnis genommen');


# Error messages
define('ERROR_SESSION_SAVE_PATH', 'Die Session konnte nicht gestartet werden. Bitte setze die Dateirechte des Ordners %s auf 777 (volle Schreib- und Leserechte).');
define('ERROR_SET_PERMISSIONS_FAILED', 'Das Setzen der Rechte ist leider fehlgeschlagen. Versuche die Rechte nun manuell zu setzen.');
define('ERROR_TABLES_EXIST', 'Die Datenbank enth&auml;lt bereits Tabellen!');
define('ERROR_FTP_CONNECTION', 'Es konnte keine FTP-Verbindung zu \'%s\' hergestellt werden. &Uuml;berpr&uuml;fe die FTP-Adresse!');
define('ERROR_FTP_DATA', 'Der FTP-Benutzer \'%s\' oder das FTP-Passwort ist falsch!');
define('ERROR_UNEXPECTED', 'Ein unerwarteter Fehler ist aufgetreten. Beginne die Installation nochmals.');
define('ERROR_CONFIG_FILES', 'Die Konfigurationsdateien konnten nicht geschrieben werden, da sie keine Schreibrechte (777) haben.');
define('ERROR_MISSING_FILES', 'Shop unvollst&auml;ndig hochgeladen');
define('ERROR_DB_QUERY', '-Befehle k&ouml;nnen aufgrund fehlender Rechte des MySQL-Benutzers nicht ausgef&uuml;hrt werden. Wende dich an deinen Provider mit der Bitte die Rechte des MySQL-Benutzers entsprechend anzupassen.');
define('ERROR_SFTP_CONNECTION', 'Es konnte keine SFTP-Verbindung hergestellt werden. &Uuml;berpr&uuml;fe bitte die Zugangsdaten!');

define('ERROR_INPUT_DB_CONNECTION', 'Server, Benutzer oder Passwort sind ung&uuml;ltig');
define('ERROR_INPUT_DB_DATABASE', 'Datenbank existiert nicht');
define('ERROR_INPUT_DB_DATABASE_NAMING', 'Datenbankname enth&auml;lt ung&uuml;ltige Zeichen');
define('ERROR_INPUT_SERVER_URL', 'Shop unter dieser Adresse nicht erreichbar');
define('ERROR_INPUT_SERVER_HTTPS', 'SSL-Aktivierung ohne g&uuml;ltiges Zertifikat kann Probleme verursachen');
define('ERROR_INPUT_MIN_LENGTH_1', 'Mindestzeichenanzahl von 1 nicht erreicht');
define('ERROR_INPUT_MIN_LENGTH_2', 'Mindestzeichenanzahl von 2 nicht erreicht');
define('ERROR_INPUT_EMAIL', 'E-Mail-Adresse ung&uuml;ltig');
define('ERROR_INPUT_MIN_LENGTH_3', 'Mindestzeichenanzahl von 3 nicht erreicht');
define('ERROR_INPUT_MIN_LENGTH_4', 'Mindestzeichenanzahl von 4 nicht erreicht');
define('ERROR_INPUT_MIN_LENGTH_5', 'Mindestzeichenanzahl von 5 nicht erreicht');
define('ERROR_INPUT_PASSWORD_CONFIRMATION', 'Wiederholung und Passwort nicht identisch');
define('ERROR_MEMORY_LIMIT', '&quot;memory_limit&quot; zu niedrig');
define('ERROR_TEXT_MEMORY_LIMIT', 'In der Serverkonfiguration ist f&uuml;r das &quot;memory_limit&quot; ein zu niedriger Wert gesetzt, um alle Funktionen des Shops nutzen zu k&ouml;nnen. Wir empfehlen als Wert mindestens %sM.<br />Wende dich an deinen Provider mit der Bitte das &quot;memory_limit&quot; entsprechend zu erh&ouml;hen.');

define('REQUIREMENT_WARNING', '<p>Für den Gambio Shop wird mindestens <strong>PHP %s </strong> benötigt.</p>
<p>Deine PHP-Version: <strong>%s</strong>
<p>Bitte aktualisiere die Version bei deinem Hosting-Anbieter.</p>');
define('LABEL_ERROR_REPORTS', 'Ich möchte zur Verbesserung der Software beitragen und stimme zu, dass beim Auftreten eines Fehlers automatisch ein Fehlerbericht an Gambio gesendet wird.');
define('TEXT_ERROR_REPORTS', '
<p>
    Fehlerberichte enthalten
    <ul>
        <li>Server-Informationen (z.B. PHP- und mySQL-Versionen, Einstellungen, geladene Module)</li>
        <li>Laufzeit-Informationen (z.B. Script-Name/URL, Sprache, IP, Zeitstempel, Browser, genutzte Parameter)</li>
        <li>Fehlerdetails (z.B. Fehlermeldung, betroffener Code-Abschnitt)</li>
    </ul>
</p>
<div>
    Fehlerberichte können unter Umständen auch persönliche Daten enthalten, z. B. wenn es zu Fehlern während der Verarbeitung von Bestelldaten kommt. Bitte prüfe, ob eine Informierung deiner Kunden, zum Beispiel im Rahmen der Datenschutzerklärung, nötig ist. Das Senden der Fehlerberichte kannst du im Modul-Center jederzeit wieder deaktivieren.
</div>');

# Admin Feed
define('TEXT_ADMIN_FEED_SHOP_INFORMATION',  '
<p>
    Technische Informationen sind z.B.
    <ul>
        <li>Versionsinformationen zu deinem Shop</li>
        <li>installierte Module und Updates, aktive Sprachen, aktive Länder, usw.</li>
        <li>Server-Informationen (z.B. PHP- und mySQL-Versionen, Einstellungen, geladene Module)</li>
    </ul>
</p>
<p>
    Die Informationen enthalten dabei keine personen- oder handelsbezogenen Daten. Das Senden der technischen Informationen kannst du im Adminbereich unter “Shop-Einstellungen” jederzeit wieder deaktivieren.
</p>
<div>
    Vielen Dank, dass du uns hilfst Gambio noch besser zu machen!
</div>');
define('CHECKBOX_ADMIN_FEED_SHOP_INFORMATION', 'Technische Informationen meiner Shop-Installation an Gambio senden.');

define('ERROR_MARIADB_REQUIREMENTS', 'MariaDB-Version %s ist zu alt. Du benötigst mindestens Version %s.');
define('ERROR_MYSQL_REQUIREMENTS', 'MySQL-Version %s ist zu alt. Du benötigst mindestens Version %s.');
