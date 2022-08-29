<?php
/**
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @category  MageSpark
 * @package   MageSpark\Base
 * @author    MageSpark team <support@magespark.com>
 * @copyright 2020 MageSpark
 */

namespace MageSpark\Base\Model\AdminNotification;

use Magento\Backend\Model\Session;

/**
 * Class Messages
 * @package MageSpark\Base\Model\AdminNotification
 */
class Messages
{
    /**
     * const AMBASE_SESSION_IDENTIFIER
     */
    const AMBASE_SESSION_IDENTIFIER = 'msbase-session-messages';

    /**
     * @var Session
     */
    private $session;

    /**
     * Messages constructor.
     *
     * @param Session $session
     */
    public function __construct(
        Session $session
    ) {
        $this->session = $session;
    }

    /**
     * Add messages
     *
     * @param string $message
     */
    public function addMessage($message)
    {
        $messages = $this->session->getData(self::AMBASE_SESSION_IDENTIFIER);
        if (!$messages || !is_array($messages)) {
            $messages = [];
        }

        $messages[] = $message;
        $this->session->setData(self::AMBASE_SESSION_IDENTIFIER, $messages);
    }

    /**
     * Get messages
     *
     * @return array
     */
    public function getMessages()
    {
        $messages = $this->session->getData(self::AMBASE_SESSION_IDENTIFIER);
        $this->clear();
        if (!$messages || !is_array($messages)) {
            $messages = [];
        }

        return $messages;
    }

    /**
     * Save the session identifier
     *
     */
    public function clear()
    {
        $this->session->setData(self::AMBASE_SESSION_IDENTIFIER, []);
    }
}
