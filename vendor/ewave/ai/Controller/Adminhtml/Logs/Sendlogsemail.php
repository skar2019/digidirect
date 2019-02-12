<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ewave\AI\Controller\Adminhtml\Logs;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Ewave\AI\Helper\Logger as LoggerHelper;

/**
 * Class Sendlogsemail
 * @package Ewave\AI\Controller\Adminhtml\Logs
 */
class Sendlogsemail extends \Magento\Backend\App\Action
{
    /**
     * TransportBuilder
     *
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;

    /**
     * LoggerHelper
     *
     * @var LoggerHelper
     */
    protected $_logHelper;

    /**
     * ScopeConfigInterface
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * StateInterface
     *
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;

    /**
     * @var \Ewave\AI\Model\Engine\Mail\Mail
     */
    protected $mailer;

    /**
     * @var array|\Ewave\AI\Model\Logger\LoggerFactory
     */
    protected $loggerFactory;

    /**
     * Sendlogsemail constructor.
     * @param Context $context
     * @param \Ewave\AI\Model\Engine\Mail\MailFactory $mail
     * @param \Ewave\AI\Model\Logger\Types\DbFactory $loggerFactory
     */
    public function __construct(
        Context $context,
        \Ewave\AI\Model\Engine\Mail\MailFactory $mail,
        \Ewave\AI\Model\Logger\Types\DbFactory $loggerFactory
    ) {
        parent::__construct($context);
        $this->mailer = $mail;
        $this->loggerFactory = $loggerFactory;
    }

    /**
     * Check the permission to run it
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Ewave_AI::logs');
    }

    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $emails = $this->getRequest()->getParam('emails', []);
        $logId = $this->getRequest()->getParam('log_id', false);
        $attachLogFile = $this->getRequest()->getParam('attach-log-file', false);

        $result = [
            'error' => false,
            'msg' => __('Emails have been sent')
        ];

        if (!count($emails) || !$logId) {
            $result['error'] = true;
            $result['msg'] = __('Wrong Data, impossible to send email');
            return $this->sendResponse($result);
        }

        /**@var \Ewave\AI\Model\Logger\Logger * */

        $log = $this->loggerFactory->create();
        $log->getResource()->load($log, $logId);
        $log->getResource()->attachLogData($log);

        $this->mailer->create()->sendLogEmail($log, $emails, $attachLogFile);
        return $this->sendResponse($result);
    }

    /**
     * Send json response
     *
     * @param [] $result
     * @return string
     */
    protected function sendResponse($result)
    {
        return $this->getResponse()->representJson(
            $this->_objectManager->get(\Magento\Framework\Json\Helper\Data::class)->jsonEncode($result)
        );
    }
}
