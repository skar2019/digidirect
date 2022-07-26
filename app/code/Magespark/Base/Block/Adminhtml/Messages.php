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

namespace MageSpark\Base\Block\Adminhtml;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use MageSpark\Base\Model\AdminNotification\Messages as AdminMessages;
use Magento\Framework\App\Request\Http;

/**
 * Class Messages
 *
 * @package MageSpark\Base\Block\Adminhtml
 */
class Messages extends Template
{
    const MAGESPARK_BASE_SECTION_NAME = 'magespark_base';

    /**
     * @var AdminMessages
     */
    private $messageManager;

    /**
     * @var Http
     */
    private $request;

    /**
     * Messages constructor.
     *
     * @param Context $context
     * @param AdminMessages $messageManager
     * @param Http $request
     * @param array $data
     */
    public function __construct(
        Context $context,
        AdminMessages $messageManager,
        Http $request,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->messageManager = $messageManager;
        $this->request = $request;
    }

    /**
     * Get the message by messagemanager
     *
     * @return array
     */
    public function getMessages()
    {
        return $this->messageManager->getMessages();
    }

    /**
     * Used to produce html output
     * @return string
     */
    public function _toHtml()
    {
        $html  = '';
        if ($this->request->getParam('section') == self::MAGESPARK_BASE_SECTION_NAME) {
            $html = parent::_toHtml();
        }

        return $html;
    }
}
