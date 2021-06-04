<?php
namespace Ewave\Emailblocker\Plugin;

use Ewave\Emailblocker\Helper\Data;

/**
 * Class TransportBuilderPlugin
 * @package Ewave\Emailblocker\Plugin
 */
class TransportBuilderPlugin
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * TransportBuilderPlugin constructor.
     * @param Data $helper
     */
    public function __construct(Data $helper)
    {
        $this->helper = $helper;
    }

    /**
     * @param \Magento\Framework\Mail\Template\TransportBuilder $subject
     * @param \Closure $proceed
     * @param string|array $address
     * @param string $name
     * @return $this
     */
    public function aroundAddTo(
        \Magento\Framework\Mail\Template\TransportBuilder $subject,
        \Closure $proceed,
        $address,
        $name = ''
    ) {
        if (is_array($address)) {
            foreach ($address as $key => $email) {
                if ($this->helper->needToBlock($email)) {
                    $this->helper->logBlockedAddress($email);
                    unset($address[$key]);
                }
            }
            if (empty($address)) {
                return $subject;
            }
        } else {
            if ($this->helper->needToBlock($address)) {
                $this->helper->logBlockedAddress($address);
                return $subject;
            }
        }
        return $proceed($address, $name);
    }

    /**
     * @param \Magento\Framework\Mail\Template\TransportBuilder $subject
     * @param \Closure $proceed
     * @return void
     * @throws \Magento\Framework\Exception\MailException
     */
    public function aroundSendMessage(
        \Magento\Framework\Mail\Template\TransportBuilder $subject,
        \Closure $proceed
    ) {
        if ($this->helper->emailBlockerIsEnabled() && empty($recipients)) {
            return;
        }
        return $proceed();
    }
}
