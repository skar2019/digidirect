<?php

namespace Ewave\AbstractGiftCardLogger\Model;

use Ewave\AI\Model\Logger\Logger as AILogger;

class Logger extends \Ewave\AI\Model\Logger\Logger
{
    /**
     * @var string
     */
    protected $_commandCode;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Logger constructor.
     * @param \Ewave\AI\Helper\Logger $logHelper
     * @param \Ewave\AI\Model\Logger\Types\DbFactory $dbLoggerFactory
     * @param \Ewave\AI\Model\Logger\Types\FileFactory $fileLoggerFactory
     * @param \PSR\Log\LoggerInterface $psrLogger
     * @param \Ewave\AI\Model\Integrations\IntegrationsFactory $integrationsFactory
     * @param \Ewave\AI\Model\Logger\Types\EmailFactory $emailLoggerFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param null $integration
     * @param array $alternativeLoggers
     * @param string $commandCode
     */
    public function __construct(
        \Ewave\AI\Helper\Logger $logHelper,
        \Ewave\AI\Model\Logger\Types\DbFactory $dbLoggerFactory,
        \Ewave\AI\Model\Logger\Types\FileFactory $fileLoggerFactory,
        \PSR\Log\LoggerInterface $psrLogger,
        \Ewave\AI\Model\Integrations\IntegrationsFactory $integrationsFactory,
        \Ewave\AI\Model\Logger\Types\EmailFactory $emailLoggerFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        $integration = null,
        $alternativeLoggers = [],
        $commandCode = null
    ) {
        if (!$integration instanceof \Ewave\AI\Model\Integrations\Integrations) {
            $integration = $integrationsFactory->create([
                'data' => [
                    'integration_name'  => 'AbstractGiftCard',
                    'process_code'      => 'AbstractGiftCard'
                ]
            ]);
        }

        $alternativeLoggers[self::LOG_PLACE_EMAIL] = $emailLoggerFactory->create([
            'data' => ['logger' => $this],
            'subject' => __('AbstractGiftCard Integration Log'),
        ]);

        $this->_commandCode = $commandCode;
        $this->storeManager = $storeManager;
        parent::__construct(
            $logHelper,
            $dbLoggerFactory,
            $fileLoggerFactory,
            $psrLogger,
            $integration,
            $alternativeLoggers
        );
    }

    /**
     * @param mixed $level
     * @param string $message
     * @param array $context
     * @param string $place
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @return Logger
     */
    public function log($level, $message, array $context = [], $place = AILogger::LOG_PLACE_FILE_AND_DB)
    {
        if (!$this->getDbLogger()->getId()) {
            $this->addHeader('Initiator: AbstractGiftCard. ' . $this->getCommandCode(), null, AILogger::LOG_PLACE_DB);
            $website = $this->storeManager->getWebsite();
            if ($website->getId()) {
                $this->addIdentifyingParams(['website' => $website->getCode()], $place);
            }
        }

        if (in_array($level, [\Monolog\Logger::ERROR, \Monolog\Logger::CRITICAL])) {
            $place = AILogger::LOG_PLACE_EVERYWHERE;
        }

        return parent::log($level, $message, $context, $place);
    }

    /**
     * @param string $code
     * @return $this
     */
    public function setCommandCode($code)
    {
        $this->_commandCode = $code;
        return $this;
    }

    /**
     * @return string
     */
    public function getCommandCode()
    {
        return $this->_commandCode;
    }
}
