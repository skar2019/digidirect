<?php

namespace Digidirect\SftpDownload\Model\Mail;

class TransportBuilder extends \Magento\Framework\Mail\Template\TransportBuilder
{
    
    public function __construct(
        FactoryInterface $templateFactory, 
        MessageInterface $message, 
        SenderResolverInterface $senderResolver, 
        ObjectManagerInterface $objectManager, 
        TransportInterfaceFactory $mailTransportFactory, 
        MessageInterfaceFactory $messageFactory = null, 
        EmailMessageInterfaceFactory $emailMessageInterfaceFactory = null, 
        MimeMessageInterfaceFactory $mimeMessageInterfaceFactory = null, 
        MimePartInterfaceFactory $mimePartInterfaceFactory = null, 
        AddressConverter $addressConverter = null
    ){
        parent::__construct(
            $templateFactory, 
            $message, 
            $senderResolver, 
            $objectManager, 
            $mailTransportFactory, 
            $messageFactory, 
            $emailMessageInterfaceFactory, 
            $mimeMessageInterfaceFactory, 
            $mimePartInterfaceFactory, 
            $addressConverter
        );
    }
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

