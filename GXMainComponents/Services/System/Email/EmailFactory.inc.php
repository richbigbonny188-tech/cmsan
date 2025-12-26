<?php
/* --------------------------------------------------------------
   EmailFactory.inc.php 2023-11-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

use Gambio\Core\Application\ValueObjects\ShopInformation;
use Gambio\Core\Configuration\Services\ConfigurationService;
use Gambio\Core\TextManager\Services\TextManager;
use PHPMailer\PHPMailer\PHPMailer;

MainFactory::load_class('EmailFactoryInterface');

/**
 * Class EmailFactory
 *
 * @category System
 * @package  Email
 */
class EmailFactory implements EmailFactoryInterface
{
    /**
     * Query builder.
     *
     * @var CI_DB_query_builder
     */
    protected $db;
    
    /**
     * @var \EmailMailerSettings
     */
    protected $mailerSettings;
    
    
    /**
     * Class Constructor
     *
     * @param CI_DB_query_builder $db Query builder.
     */
    public function __construct(CI_DB_query_builder $db)
    {
        $this->db = $db;
    }
    
    
    /**
     * Creates an email object
     *
     * @param IdType                        $id           (optional) E-Mail ID.
     * @param EmailSubjectInterface         $subject      (optional) E-Mail subject.
     * @param EmailContentInterface         $contentPlain (optional) E-Mail plain content.
     * @param EmailContentInterface         $contentHtml  (optional) E-Mail HTML content.
     * @param bool                          $p_isPending  (optional) E-Mail is pending?
     * @param ContactCollectionInterface    $contacts     (optional) E-Mail contacts.
     * @param AttachmentCollectionInterface $attachments  (optional) E-Mail attachments.
     *
     * @return Email The created email.
     * @throws InvalidArgumentException If '$p_isPending' is not a bool, or if any other argument is not valid.
     * @throws UnexpectedValueException If the contact type was not found
     *
     */
    public function createEmail(
        IdType                        $id = null,
        EmailSubjectInterface         $subject = null,
        EmailContentInterface         $contentHtml = null,
        EmailContentInterface         $contentPlain = null,
                                      $p_isPending = true,
        ContactCollectionInterface    $contacts = null,
        AttachmentCollectionInterface $attachments = null
    ) {
        if (!is_bool($p_isPending)) {
            throw new InvalidArgumentException('Invalid $p_isPending argument given (bool expected): '
                                               . print_r($p_isPending, true));
        }
        
        $email = MainFactory::create('Email');
        
        // Set email information.
        if ($id !== null) {
            $email->setId($id);
        }
        
        if ($subject !== null) {
            $email->setSubject($subject);
        }
        
        if ($contentPlain !== null) {
            $email->setContentPlain($contentPlain);
        }
        
        if ($contentHtml !== null) {
            $email->setContentHtml($contentHtml);
        }
        
        $email->setPending($p_isPending);
        
        // Set email contacts.
        if ($contacts !== null) {
            foreach ($contacts->getArray() as $contact) {
                switch ($contact->getContactType()) {
                    case ContactType::SENDER:
                        $email->setSender($contact);
                        break;
                    case ContactType::RECIPIENT:
                        $email->setRecipient($contact);
                        break;
                    case ContactType::REPLY_TO:
                        $email->setReplyTo($contact);
                        break;
                    case ContactType::BCC:
                        $email->getBcc()->add($contact);
                        break;
                    case ContactType::CC:
                        $email->getCc()->add($contact);
                        break;
                    default:
                        throw new UnexpectedValueException('Unexpected contact type: ' . $contact->getContactType());
                }
            }
        }
        
        // Set email attachments collection.
        if ($attachments !== null) {
            $email->setAttachments($attachments);
        }
        
        return $email;
    }
    
    
    /**
     * Creates an email contact object
     *
     * @param EmailAddressInterface $emailAddress Email address of the contact.
     * @param ContactTypeInterface  $contactType  Contact type (see ContactType class definition).
     * @param ContactNameInterface  $contactName  (optional) Contact display name.
     *
     * @return EmailContact The created email contact.
     */
    public function createContact(
        EmailAddressInterface $emailAddress,
        ContactTypeInterface  $contactType,
        ContactNameInterface  $contactName = null
    ) {
        return MainFactory::create('EmailContact', $emailAddress, $contactType, $contactName);
    }
    
    
    /**
     * Creates an email attachment object
     *
     * @param AttachmentPathInterface $path Valid path of the attachment (on the server).
     * @param AttachmentNameInterface $name (optional) Display name for the attachment.
     *
     * @return EmailAttachment The created email attachment.
     */
    public function createAttachment(AttachmentPathInterface $path, AttachmentNameInterface $name = null)
    {
        return MainFactory::create('EmailAttachment', $path, $name);
    }
    
    
    /**
     * Creates a mailer adapter object
     *
     * @return MailerAdapter The created mailer adapter.
     */
    public function createMailerAdapter()
    {
        $mailer               = $this->createMailer();
        $punycode             = $this->createPunycodeEncoder();
        $useHtmlMail          = new \BoolType($this->mailerSettings->useHtmlMail());
        $shopInfo             = LegacyDependencyContainer::getInstance()->get(ShopInformation::class);
        $configurationService = LegacyDependencyContainer::getInstance()->get(ConfigurationService::class);
        $textManager          = LegacyDependencyContainer::getInstance()->get(TextManager::class);
        
        return MainFactory::create('MailerAdapter',
                                   $mailer,
                                   $punycode,
                                   $useHtmlMail,
                                   $shopInfo,
                                   $configurationService,
                                   $textManager);
    }
    
    
    /**
     * Creates a PHP Punycode encoder instance.
     *
     * @link https://github.com/true/php-punycode
     *
     * @return \TrueBV\Punycode
     */
    public function createPunycodeEncoder()
    {
        $punycode = new \TrueBV\Punycode();
        
        return $punycode;
    }
    
    
    /**
     * Creates a PHP mailer object.
     *
     * @param string                    $protocol       (Optional) Provide 'smtp', 'sendmail' or 'mail' if you want to
     *                                                  override the EMAIL_TRANSPORT constant.
     *
     * @param \EmailMailerSettings|null $mailerSettings (Optional) settings value object for mailer.
     *
     * @return PHPMailer The created PHP mailer.
     */
    public function createMailer($protocol = null, EmailMailerSettings $mailerSettings = null)
    {
        if (!$mailerSettings) {
            $mailerSettings = $this->_buildMailerSettings();
        }
        
        $mailer            = new PHPMailer(true);
        $mailer->SMTPDebug = 0; // Disable debug output.
        
        // how it should look like:
        $mailer->CharSet = $mailerSettings->charset();
        $mailer->setLanguage($mailerSettings->languageCode(), $mailerSettings->languagePath());
        
        // Set PHPMailer Protocol
        $protocol = $protocol ? : $mailerSettings->emailTransport();
        
        switch ($protocol) {
            case 'smtp':
                $mailer->IsSMTP();
                // Set mailer to use SMTP
                $mailer->SMTPKeepAlive = true;
                // Turn on SMTP authentication
                $mailer->SMTPAuth = filter_var($mailerSettings->auth(), FILTER_VALIDATE_BOOLEAN);
                // SMTP username
                $mailer->Username = $mailerSettings->user();
                // SMTP password
                $mailer->Password = $mailerSettings->password();
                // Specify main and backup server "smtp1.example.com;smtp2.example.com"
                $mailer->Host = $mailerSettings->mainServer() . ';' . $mailerSettings->backupServer();
                // Set SMTP Port
                $mailer->Port = $mailerSettings->port();
                if ($mailerSettings->encryption() === 'ssl' || $mailerSettings->encryption() === 'tls') {
                    $mailer->SMTPSecure = $mailerSettings->encryption();
                }
                break;
            
            case 'sendmail':
                $mailer->IsSendmail();
                $mailer->Sendmail = $mailerSettings->sendMailPath();
                break;
            
            case 'mail':
                $mailer->IsMail();
                break;
        }
        
        return $mailer;
    }
    
    
    /**
     * Builds and returns the mailer settings.
     *
     * @return bool|\EmailMailerSettings
     */
    protected function _buildMailerSettings()
    {
        if (null === $this->mailerSettings) {
            $configProvider = MainFactory::create('EmailConfigurationProvider', $this->db);
            
            $this->mailerSettings = MainFactory::create('EmailMailerSettings',
                                                        $this->_string($configProvider->charset()),
                                                        $this->_string($configProvider->languageCode()),
                                                        $this->_string(DIR_WS_CLASSES),
                                                        $this->_string($configProvider->emailTransport()),
                                                        $this->_string($configProvider->mainServer()),
                                                        $this->_string($configProvider->backupServer()),
                                                        $this->_string($configProvider->auth()),
                                                        $this->_string($configProvider->user()),
                                                        $this->_string($configProvider->password()),
                                                        $this->_string($configProvider->port()),
                                                        $this->_string($configProvider->encryption()),
                                                        $this->_string($configProvider->sendMailPath()),
                                                        new \BoolType($configProvider->useHtmlMail()));
        }
        
        return $this->mailerSettings;
    }
    
    
    /**
     * Creates an email service object
     *
     * @return EmailService The created email service.
     */
    public function createService()
    {
        return MainFactory::create('EmailService',
                                   $this->createRepository(),
                                   $this,
                                   $this->createMailerAdapter(),
                                   $this->createAttachmentsHandler());
    }
    
    
    /**
     * Creates an email repository object
     *
     * @return EmailRepository The created email repository.
     */
    public function createRepository()
    {
        return MainFactory::create('EmailRepository',
                                   $this->createWriter(),
                                   $this->createReader(),
                                   $this->createDeleter());
    }
    
    
    /**
     * Creates an email writer object
     *
     * @return EmailWriter The created email writer.
     */
    public function createWriter()
    {
        return MainFactory::create('EmailWriter', $this->_getDbConnection());
    }
    
    
    /**
     * Create EmailReader Object
     *
     * @return EmailReader The created email deleter.
     */
    public function createReader()
    {
        return MainFactory::create('EmailReader', $this->_getDbConnection(), $this);
    }
    
    
    /**
     * Creates email deleter object
     *
     * @return EmailDeleter The created email deleter.
     */
    public function createDeleter()
    {
        return MainFactory::create('EmailDeleter', $this->_getDbConnection());
    }
    
    
    /**
     * Creates an attachments handler object
     *
     * @param string $p_uploadsDirPath (optional) You can specify a custom uploads directory path if you do not want
     *                                 the default "uploads" directory. The path must contain a "tmp" and an
     *                                 "attachments" directory otherwise the AttachmentsHandler class will not work
     *                                 properly.
     *
     * @return AttachmentsHandler The created attachments handler.
     */
    public function createAttachmentsHandler($p_uploadsDirPath = null)
    {
        $uploadsDirPath = (!empty($p_uploadsDirPath)) ? $p_uploadsDirPath : DIR_FS_CATALOG . 'uploads';
        $attachmentConfigurationServiceFactory = MainFactory::create('AttachmentConfigurationServiceFactory');
        
        return MainFactory::create('AttachmentsHandler', $uploadsDirPath, $attachmentConfigurationServiceFactory->createService());
    }
    
    
    /**
     * Returns a database connection.
     *
     * @return CI_DB_query_builder Database connection.
     */
    protected function _getDbConnection()
    {
        return $this->db;
    }
    
    
    /**
     * Returns a new string type.
     *
     * @param $string String to be represented.
     *
     * @return \StringType
     */
    protected function _string($string)
    {
        return new \StringType($string);
    }
}
