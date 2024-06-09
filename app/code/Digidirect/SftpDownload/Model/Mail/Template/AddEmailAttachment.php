<?php
namespace Digidirect\SftpDownload\Model\Mail\Template;

class AddEmailAttachment extends \Magento\Framework\Mail\Template\TransportBuilder
{

    public function addAttachment($file_content,$file_name,$file_type) 
    {
        $encoding = \Zend_Mime::ENCODING_BASE64;
        $this->message->setBodyAttachment($file_content, $file_name, $file_type, $encoding);
        return $this;
    }
    protected function prepareMessage()
    {
        parent::prepareMessage();
        $this->message->setPartsToBody();
        return $this;
    }
}