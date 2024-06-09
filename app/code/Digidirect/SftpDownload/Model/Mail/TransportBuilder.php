<?php

namespace Digidirect\SftpDownload\Model\Mail;

use Zend_Mime;

class TransportBuilder extends \Magento\Framework\Mail\Template\TransportBuilder
{
    private $parts = [];

    /**
     * @param $body
     * @param null $filename
     * @param string $mimeType
     * @param string $disposition
     * @param string $encoding
     * @return $this
     */
    public function addAttachment(
        $body,
        $mimeType    = Zend_Mime::TYPE_OCTETSTREAM,
        $filename    = null,
        $disposition = Zend_Mime::DISPOSITION_ATTACHMENT,
        $encoding    = Zend_Mime::ENCODING_BASE64
    ) {
        if (method_exists($this->message, 'createAttachment')) {
            $this->message->createAttachment(
                $body,
                $mimeType,
                $disposition,
                $encoding,
                $filename
            );
        } else {
            $mp = new \Zend\Mime\Part($body);
            $mp->encoding = $encoding;
            $mp->type = $mimeType;
            $mp->disposition = $disposition;
            $mp->filename = $filename;

            $this->parts[] = $mp;
        }

        return $this;
    }
}

