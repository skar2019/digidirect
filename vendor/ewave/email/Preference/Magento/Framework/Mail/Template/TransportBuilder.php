<?php
namespace Ewave\Email\Preference\Magento\Framework\Mail\Template;

/**
 * Class TransportBuilder
 * @package Ewave\Email\Preference\Magento\Framework\Mail\Template
 */
class TransportBuilder extends \Magento\Framework\Mail\Template\TransportBuilder
{
    /**
     * Retrieve recipients
     *
     * @return string
     */
    public function getTo()
    {
        $recipients = $this->message->getRecipients();
        return reset($recipients);
    }

    /**
     * Retrieve template variables
     *
     * @return array
     */
    public function getTemplateVars()
    {
        return $this->templateVars;
    }

    /**
     * @return \Magento\Framework\Mail\Message|\Magento\Framework\Mail\MessageInterface
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $attachment
     * @param string $type
     * @param string $disposition
     * @param string $encoding
     * @param string $name
     * @return $this
     */
    public function createAttachment(
        $attachment,
        $type,
        $disposition,
        $encoding,
        $name
    ) {
        $this->message->createAttachment(
            $attachment,
            $type,
            $disposition,
            $encoding,
            $name
        );
        return $this;
    }
}
