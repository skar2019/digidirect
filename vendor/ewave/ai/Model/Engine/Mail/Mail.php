<?php

namespace Ewave\AI\Model\Engine\Mail;

/**
 * Class Mail
 *
 * @package Ewave\AI\Helper
 */
class Mail
{
    /**
     * Scope Config Interface
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * Directory List
     *
     * @var \Magento\Framework\App\Filesystem\DirectoryList
     */
    protected $_directoryList;

    /**
     * LoggerHelper
     *
     * @var \Ewave\AI\Helper\Mail
     */
    protected $_mailHelper;

    /**
     * LoggerHelper
     *
     * @var \Ewave\AI\Helper\Logger
     */
    protected $_logHelper;

    /**
     * Inline Translation
     *
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;

    /**
     * Transport Builder
     *
     * @var \Ewave\AI\Model\Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $transportBuilder;

    /**
     * Mail constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\App\Filesystem\DirectoryList $directoryList
     * @param \Ewave\AI\Helper\Mail $mailHelper
     * @param \Ewave\AI\Helper\Logger $logHelper
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Ewave\AI\Model\Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Ewave\AI\Helper\Mail $mailHelper,
        \Ewave\AI\Helper\Logger $logHelper,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Ewave\AI\Model\Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
    ) {
        $this->_logHelper = $logHelper;
        $this->_mailHelper = $mailHelper;
        $this->_scopeConfig = $context->getScopeConfig();
        $this->_directoryList = $directoryList;
        $this->inlineTranslation = $inlineTranslation;
        $this->transportBuilder = $transportBuilder;
    }

    /**
     * @param \Ewave\AI\Model\Logger\Types\Db $log
     * @param array $emails
     * @param bool $attachLogFile
     * @return bool
     */
    public function sendLogEmail(\Ewave\AI\Model\Logger\Types\Db $log, $emails, $attachLogFile = false)
    {
        if (!count($emails)) {
            return false;
        }

        $postObject = new \Magento\Framework\DataObject();
        $postObject->setData($log->getData());

        $this->inlineTranslation->suspend();

        //reset previous data
        $this->transportBuilder->resetObjectState();
        $transport = $this->transportBuilder
            ->setTemplateIdentifier($this->_logHelper->getEmailTemplate())
            ->setTemplateOptions(
                [
                    'area' => \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE,
                    'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                ]
            )
            ->setTemplateVars(['data' => $postObject])
            ->setFrom(
                $this->_scopeConfig->getValue(
                    \Magento\Contact\Controller\Index::XML_PATH_EMAIL_SENDER,
                    \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
                )
            )
            ->addTo($emails);

        if ($attachLogFile && $log->getLogFile() && file_exists($log->getLogFile())) {
            $transport->attachFile($log->getLogFile(), basename($log->getLogFile()));
        }

        $transport->getTransport()->sendMessage();
        $this->inlineTranslation->resume();

        return true;
    }

    /**
     * @param string $msg
     * @param null $log
     * @param bool $globalFailTemplate
     * @return bool
     */
    public function sendFailEmail($msg, $log = null, $globalFailTemplate = false)
    {
        $emails = $this->_mailHelper->getFailEmails();
        $templateId = $this->_mailHelper->_getFailEmailTemplate($globalFailTemplate);
        $dataObject = new \Magento\Framework\DataObject();
        if ($log) {
            $dataObject->setData($log->getData());
        }
        $dataObject->setData('message', $msg);
        $data = ['data' => $dataObject];
        return $this->sendEmail($templateId, $emails, $data);
    }

    /**
     * @param \Ewave\AI\Api\Data\QueueInterface $queue
     * @param array $runOptions
     * @return bool
     */
    public function sendQueueFailEmail(\Ewave\AI\Api\Data\QueueInterface $queue, $runOptions)
    {
        $emails = $this->_mailHelper->getQueueFailEmails();
        $templateId = $this->_mailHelper->getQueueFailEmailTemplate();
        $queueObject = new \Magento\Framework\DataObject();
        $queueObject->setData($queue->getData());
        $queueObject->setData('run_options', $this->_logHelper->varExportForLog($runOptions));
        $data = ['queue' => $queueObject];
        return $this->sendEmail($templateId, $emails, $data);
    }

    /**
     * @param int $templateId
     * @param array $emails
     * @param array $data
     * @return bool
     */
    protected function sendEmail($templateId, $emails, $data)
    {
        if (empty($emails)) {
            return false;
        }

        $this->inlineTranslation->suspend();
        $this->transportBuilder->resetObjectState();

        $transport = $this->transportBuilder
            ->setTemplateIdentifier($templateId)
            ->setTemplateOptions(
                [
                    'area' => \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE,
                    'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                ]
            )
            ->setTemplateVars($data)
            ->setFrom(
                $this->_scopeConfig->getValue(
                    \Magento\Contact\Controller\Index::XML_PATH_EMAIL_SENDER,
                    \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
                )
            )
            ->addTo($emails);

        $transport->getTransport()->sendMessage();
        $this->inlineTranslation->resume();

        return true;
    }
}
