<?php

namespace Ewave\CheckoutFields\Plugin\Customer\Model;

use Magento\Customer\Model\Customer\NotificationStorage as Subject;
use Ewave\CheckoutFields\Controller\Index\Index;

class NotificationStorage
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * NotificationStorage constructor.
     *
     * @param \Magento\Framework\App\RequestInterface $request
     */
    public function __construct(\Magento\Framework\App\RequestInterface $request)
    {
        $this->request = $request;
    }

    /**
     * @param Subject $subject
     * @param $result
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterIsExists(Subject $subject, $result)
    {
        return $result && $this->request->getFullActionName() !== Index::FULL_ACTION_NAME;
    }
}
