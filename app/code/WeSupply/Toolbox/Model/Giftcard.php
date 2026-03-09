<?php
/**
 * Created by PhpStorm.
 * User: adminuser
 * Date: 14.06.2019
 * Time: 11:32
 */

namespace WeSupply\Toolbox\Model;

use WeSupply\Toolbox\Api\GiftcardInterface;

class Giftcard implements GiftcardInterface
{


    private $giftCardAccountInterface = NULL;

    private $giftCardAccountRepository = NULL;

    private $emailManagement = NULL;

    private $giftCardAccountFactory;

    private $logger;

    private $generatedCode;


    public function __construct(
        \Magento\GiftCardAccount\Api\Data\GiftCardAccountInterfaceFactory $giftCardAccountFactory,
        \Magento\GiftCardAccount\Api\GiftCardAccountRepositoryInterface $giftCardAccountRepository,
        \Magento\GiftCardAccount\Model\EmailManagement $emailManagement,
        \WeSupply\Toolbox\Logger\Logger $logger
    )
    {
       $this->giftCardAccountFactory = $giftCardAccountFactory;
       $this->giftCardAccountRepository = $giftCardAccountRepository;
       $this->emailManagement = $emailManagement;
       $this->logger = $logger;
    }

    public function initData()
    {

        if(is_null($this->giftCardAccountInterface)){
            $this->giftCardAccountInterface = $this->giftCardAccountFactory->create();
        }
    }


    public function createAndDeliverGiftCard($giftCardAmount, $customerEmail, $customerName, $websiteId = 1)
    {

        $this->initData();

        $expirationDate = date('Y-m-d', strtotime('+1 year'));

        $card = array();
        $card['website_id'] = $websiteId;
        $card['balance'] = $giftCardAmount;
        $card['date_expires'] = $expirationDate;
        $card['status'] = 1;
        $card['is_redeemable'] = 1;
        $card['recipient_email'] = $customerEmail;
        $card['recipient_name'] = $customerName;

        try {

            $this->giftCardAccountInterface->setData($card);
            $this->giftCardAccountRepository->save($this->giftCardAccountInterface);
            $this->generatedCode = $this->giftCardAccountInterface->getCode();
            $emailSent = $this->emailManagement->sendEmail($this->giftCardAccountInterface);
            return $emailSent;

        }catch(\Exception $e){
            $this->logger->error('Error when trying to create gift card refund with message: '.$e->getMessage());
            return FALSE;
        }

    }


    public function getGeneratedCode(){
        return $this->generatedCode;
    }

}
