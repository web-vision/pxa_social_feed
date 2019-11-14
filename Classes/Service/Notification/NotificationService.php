<?php

namespace Pixelant\PxaSocialFeed\Service\Notification;

use TYPO3\CMS\Core\Mail\MailMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class ErrorImportingNotificationService
 * @package Pixelant\PxaSocialFeed\Service\Notification
 */
class NotificationService
{
    /**
     * @var string
     */
    protected $senderEmail = '';

    /**
     * @var string
     */
    protected $receiverEmail = '';

    /**
     * @param string $receiverEmail
     * @param string $senderEmail
     */
    public function __construct($receiverEmail = '', $senderEmail = '')
    {
        $this->receiverEmail = $receiverEmail;
        $this->senderEmail = $senderEmail;
    }

    /**
     * Notify by email
     *
     * @param string $subject
     * @param string $message
     */
    public function notify($subject, $message)
    {
        $mailer = $this->getMailer();

        $mailer
            ->setSubject($subject)
            ->setBody($message, 'text/html')
            ->send();
    }

    /**
     * Check if can send an email
     *
     * @return bool
     */
    public function canSendEmail()
    {
        return GeneralUtility::validEmail($this->senderEmail) && GeneralUtility::validEmail($this->receiverEmail);
    }

    /**
     * Prepare mailer
     *
     * @return MailMessage
     */
    protected function getMailer()
    {
        $mail = GeneralUtility::makeInstance(MailMessage::class);

        $mail
            ->setFrom($this->senderEmail)
            ->setTo($this->receiverEmail);

        return $mail;
    }

    /**
     * @return string
     */
    public function getSenderEmail()
    {
        return $this->senderEmail;
    }

    /**
     * @param string $senderEmail
     */
    public function setSenderEmail($senderEmail)
    {
        $this->senderEmail = $senderEmail;
    }

    /**
     * @return string
     */
    public function getReceiverEmail()
    {
        return $this->receiverEmail;
    }

    /**
     * @param string $receiverEmail
     */
    public function setReceiverEmail($receiverEmail)
    {
        $this->receiverEmail = $receiverEmail;
    }
}
