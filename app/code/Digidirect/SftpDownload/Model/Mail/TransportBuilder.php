<?php

namespace Digidirect\SftpDownload\Model\Mail;

class TransportBuilder extends \Magento\Framework\Mail\Template\TransportBuilder
{
    /**
     * @param Api\AttachmentInterface $attachment
     */
    public function addAttachment($pdfString)
    {
        $this->message->createAttachment(
            $pdfString,
            'application/pdf',
            \Zend_Mime::DISPOSITION_ATTACHMENT,
            \Zend_Mime::ENCODING_BASE64,
            'attached.pdf'
        );
        return $this;
    }
}

