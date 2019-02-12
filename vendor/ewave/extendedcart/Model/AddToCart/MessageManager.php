<?php

namespace Ewave\ExtendedCart\Model\AddToCart;

use Magento\Framework\Message\Manager;
use Ewave\ExtendedCart\Helper\AddToCartConfirmationPopup as AddToCartConfirmationPopupHelper;
use Magento\Framework\Message\Session;
use Magento\Framework\Message\Factory;
use Magento\Framework\Message\CollectionFactory;
use Magento\Framework\Message\ExceptionMessageFactoryInterface;
use Magento\Framework\Event;
use Psr\Log\LoggerInterface;

use Magento\Framework\Message\ManagerInterface;

class MessageManager extends Manager
{
    /**
     * @var AddToCartConfirmationPopupHelper
     */
    protected $confirmationPopupHelper;

    /**
     * @var bool
     */
    protected $addToCartFinishedSuccessfully = false;

    /**
     * MessageManager constructor.
     * @param Session $session
     * @param Factory $messageFactory
     * @param CollectionFactory $messagesFactory
     * @param Event\ManagerInterface $eventManager
     * @param LoggerInterface $logger
     * @param AddToCartConfirmationPopupHelper $confirmationPopupHelper
     * @param string $defaultGroup
     * @param ExceptionMessageFactoryInterface|null $exceptionMessageFactory
     */
    public function __construct(
        Session $session,
        Factory $messageFactory,
        CollectionFactory $messagesFactory,
        Event\ManagerInterface $eventManager,
        LoggerInterface $logger,
        AddToCartConfirmationPopupHelper $confirmationPopupHelper,
        $defaultGroup = self::DEFAULT_GROUP,
        ExceptionMessageFactoryInterface $exceptionMessageFactory = null
    ) {
        parent::__construct(
            $session,
            $messageFactory,
            $messagesFactory,
            $eventManager,
            $logger,
            $defaultGroup,
            $exceptionMessageFactory
        );
        $this->confirmationPopupHelper = $confirmationPopupHelper;
    }

    /**
     * Adds new success message
     *
     * @param string $message
     * @param string|null $group
     * @return ManagerInterface
     */
    public function addSuccessMessage($message, $group = null)
    {
        if ($this->confirmationPopupHelper->getIsEnabled()) {
            $this->addToCartFinishedSuccessfully = true;
            return $this;
        }
        return parent::addSuccessMessage($message, $group);
    }

    /**
     * @return bool
     */
    public function isAddToCartFinishedSuccessfully()
    {
        return $this->addToCartFinishedSuccessfully;
    }
}
