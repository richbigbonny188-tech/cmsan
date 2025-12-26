<?php
/* --------------------------------------------------------------
   MailerAdapter.inc.php 2023-11-24
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
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

MainFactory::load_class('MailerAdapterInterface');

/**
 * Class MailerAdapter
 *
 * This class provides a communication layer with the external mailing library
 * in order to isolate the library-specific code.
 *
 * @category System
 * @package  Email
 */
class MailerAdapter implements MailerAdapterInterface
{
    /**
     * PHPMailer Instance
     *
     * @var PHPMailer
     */
    protected PHPMailer $mailer;
    
    /**
     * Used for punycode encoding.
     *
     * @var TrueBV\Punycode
     */
    protected TrueBV\Punycode $punycode;
    
    /**
     * @var bool
     */
    protected bool $useHtmlMail;
    
    /**
     * @var string|null
     */
    protected ?string $systemEmailFrom = null;
    
    /**
     * @var ShopInformation|null
     */
    protected ?ShopInformation $shopInformation;
    
    /**
     * @var ConfigurationService|null
     */
    protected ?ConfigurationService $configurationService;
    
    /**
     * @var TextManager|null
     */
    protected ?TextManager $textManager;
    
    
    /**
     * Class Constructor
     *
     * @param PHPMailer                 $mailer   PHPMailer instance.
     * @param TrueBV\Punycode           $punycode Punycode encoder instance.
     * @param BoolType|null             $useHtmlMail
     * @param ShopInformation|null      $shopInformation
     * @param ConfigurationService|null $configurationService
     */
    public function __construct(
        PHPMailer            $mailer,
        TrueBV\Punycode      $punycode,
        \BoolType            $useHtmlMail = null,
        ShopInformation      $shopInformation = null,
        ConfigurationService $configurationService = null,
        TextManager          $textManager = null
    ) {
        $this->mailer               = $mailer;
        $this->punycode             = $punycode;
        $this->useHtmlMail          = $useHtmlMail && $useHtmlMail->asBool();
        $this->shopInformation      = $shopInformation;
        $this->configurationService = $configurationService;
        $this->textManager          = $textManager;
    }
    
    
    /**
     * Sends a single email.
     *
     * @param EmailInterface $email Contains email information.
     *
     * @throws AttachmentNotFoundException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \PHPMailer\PHPMailer\Exception
     */
    public function send(EmailInterface $email)
    {
        $mail = clone $this->mailer;
        
        // Set Email Sender Contact
        $mail->From     = $this->punycode->encode((string)$email->getSender()->getEmailAddress());
        $mail->FromName = (string)$email->getSender()->getContactName();
        
        // Set Email Recipient Contact
        $mail->addAddress($this->punycode->encode($this->getEmailAddress($email->getRecipient()->getEmailAddress())),
                          (string)$email->getRecipient()->getContactName());
        
        // Set Email Reply To Contact
        if ($email->getReplyTo() !== null) {
            $mail->addReplyTo($this->punycode->encode($this->getEmailAddress($email->getReplyTo()->getEmailAddress())),
                              (string)$email->getReplyto()->getContactName());
        }
        
        // Set Email BCC Contacts
        foreach ($email->getBcc()->getArray() as $contact) {
            $mail->addBCC($this->punycode->encode($this->getEmailAddress((string)$contact->getEmailAddress())),
                          (string)$contact->getContactName());
        }
        
        // Set Email CC Contacts
        foreach ($email->getCc()->getArray() as $contact) {
            $mail->addCC($this->punycode->encode($this->getEmailAddress((string)$contact->getEmailAddress())),
                         (string)$contact->getContactName());
        }
        
        // Set Email Attachments
        foreach ($email->getAttachments()->getArray() as $attachment) {
            $attachmentPath = (string)$attachment->getPath();
            if (!file_exists($attachmentPath) || !is_file($attachmentPath)) {
                throw new AttachmentNotFoundException('Attachment file does not exist or is not a file: '
                                                      . $attachmentPath, $attachmentPath);
            }
            
            $mail->addAttachment((string)$attachment->getPath(), (string)$attachment->getName());
        }
        
        // Set Email Subject and Content
        $mail->Subject  = (string)$email->getSubject();
        $additionalBody = $this->canSendOutboundEmails() ? '' : $this->textManager->getPhraseText('outbound_email_body',
                                                                                                  'emails');
        
        if ($this->useHtmlMail) {
            $mail->Encoding = PHPMailer::ENCODING_BASE64;
            $mail->CharSet  = PHPMailer::CHARSET_UTF8;
            $mail->Body     = "$additionalBody<br/>" . $email->getContentHtml();
            
            $additionalAltBody = str_replace('<br/>', PHP_EOL, $additionalBody);
            $mail->AltBody     = $additionalAltBody . PHP_EOL . $email->getContentPlain();
        } else {
            $mail->isHTML(false);
            $additionalBody = str_replace('<br/>', PHP_EOL, $additionalBody);
            $mail->Body     = $additionalBody . PHP_EOL . $email->getContentPlain();
        }
        
        // Empty mail body validation check. PHPMailer will not send mails without content.
        if ($mail->Body === '') {
            $mail->Body = PHP_EOL;
        }
        
        // Send Email
        if (!$mail->send()) {
            throw new Exception('Mailer library could not send email: ' . $mail->ErrorInfo);
        }
    }
    
    
    /**
     * Returns the email address based on the canSendOutboundEmails() method
     *
     * @param string $emailRecipient
     *
     * @return string
     */
    protected function getEmailAddress(string $emailRecipient): string
    {
        return $this->canSendOutboundEmails() ? $emailRecipient : $this->getSystemEmailFrom();
    }
    
    
    /**
     * Returns the email address set on the Settings page
     *
     * @return string|null
     */
    protected function getSystemEmailFrom(): ?string
    {
        if ($this->systemEmailFrom === null) {
            $this->systemEmailFrom = $this->configurationService->find('configuration/EMAIL_FROM')?->value();
        }
        
        return $this->systemEmailFrom;
    }
    
    
    /**
     * Returns whether the email can be sent outbounds or not
     *
     * The emails can be sent to other domain other than the shop owners' email
     * if the shop is NOT a Cloud shop or the Cloud shop HAS a contract
     *
     * @return bool
     */
    protected function canSendOutboundEmails(): bool
    {
        return $this->shopInformation->isCloud() === false || $this->shopInformation->hasContract() === true;
    }
}
