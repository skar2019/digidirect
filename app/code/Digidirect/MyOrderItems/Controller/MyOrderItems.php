<?php

namespace Digidirect\MyOrderItems\Controller;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Exception\NotFoundException;

abstract class MyOrderItems extends \Magento\Framework\App\Action\Action
{
    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @param Context $context
     * @param Session $customerSession
     */
    public function __construct(
        Context $context,
        Session $customerSession
    ) {
        parent::__construct($context);
        $this->customerSession = $customerSession;
    }

    /**
     * Dispatch request
     *
     * @param RequestInterface $request
     * @return ResponseInterface
     * @throws NotFoundException
     */
    public function dispatch(RequestInterface $request)
    {
        if (!$this->customerSession->authenticate()) {
            $this->customerSession->setAfterAuthUrl(
                $this->_url->getUrl('customer/account/login')
            );
        }
        return parent::dispatch($request);
    }
}
