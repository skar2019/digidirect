<?php
/**
 * ITORIS
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the ITORIS's Magento Extensions License Agreement
 * which is available through the world-wide-web at this URL:
 * http://www.itoris.com/magento-extensions-license.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to sales@itoris.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extensions to newer
 * versions in the future. If you wish to customize the extension for your
 * needs please refer to the license agreement or contact sales@itoris.com for more information.
 *
 * @category   ITORIS
 * @package    ITORIS_M2_CORE
 * @copyright  Copyright (c) 2016 ITORIS INC. (http://www.itoris.com)
 * @license    http://www.itoris.com/magento-extensions-license.html  Commercial License
 */
namespace Itoris\Core\Observer;

use Magento\Framework\Event\ObserverInterface;

class CheckNotifications implements ObserverInterface
{
    /** @var \Magento\Backend\Model\Auth\Session */
    private $_backendAuthSession;

    /** @var \Itoris\Core\Model\FeedFactory */
    private $feedFactory;

    /** @var \Magento\Framework\App\RequestInterface */
    private $request;

    /** @var \Magento\Framework\App\Config\Storage\WriterInterface */
    private $configWriter;

    /**
     * CheckNotifications constructor.
     * @param \Magento\Backend\Model\Auth\Session $backendAuthSession
     * @param \Itoris\Core\Model\FeedFactory $feedFactory
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\App\Config\Storage\WriterInterface $configWriter
     */
    public function __construct(
        \Magento\Backend\Model\Auth\Session $backendAuthSession,
		\Itoris\Core\Model\FeedFactory $feedFactory,
		\Magento\Framework\App\RequestInterface $request,
		\Magento\Framework\App\Config\Storage\WriterInterface $configWriter
    ) {
        $this->_backendAuthSession = $backendAuthSession;
		$this->feedFactory = $feedFactory;
		$this->request = $request;
		$this->configWriter = $configWriter;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->_backendAuthSession->isLoggedIn()) {
            $this->checkPostHash();
            $feedModel = $this->feedFactory->create();
            $feedModel->checkUpdate();
			$feedModel->notify();
        }
    }
    
    public function checkPostHash() {
        if ($this->request->getFullActionName() != 'adminhtml_system_config_save') return;
        $post = $this->request->getPost()->toArray();
        if (isset($post['groups']['installed']['fields']) && isset($post['groups']['notifications']) && is_array($post['groups']['installed']['fields'])) {
            foreach($post['groups']['installed']['fields'] as $module => $_value) {
                if (isset($_value['value'])) $this->configWriter->save('itoris_core/installed/'.$module, $_value['value'], 'default', 0);
            }
        }
    }
}
