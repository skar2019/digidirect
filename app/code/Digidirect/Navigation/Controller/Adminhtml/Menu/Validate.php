<?php
namespace Digidirect\Navigation\Controller\Adminhtml\Menu;

use \Magento\Backend\App\Action;

/**
 * Class Validate
 * @package Digidirect\Navigation\Controller\Adminhtml\Menu
 */
class Validate extends Action
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var \Digidirect\Navigation\Model\MenuFactory
     */
    protected $menuFactory;

    /**
     * Validate constructor.
     * @param Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Digidirect\Navigation\Model\MenuFactory $menu
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Digidirect\Navigation\Model\MenuFactory $menu
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->menuFactory = $menu;
    }

    /**
     * Validate
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $response = new \Magento\Framework\DataObject();
        $response->setError(false);

        $request = $this->getRequest();
        $store = (int)$request->getParam('store', \Magento\Store\Model\Store::DEFAULT_STORE_ID);
        $id = (int)$request->getParam('entity_id', 0);
        try {
            /**
             * @var $menu \Digidirect\Navigation\Model\Menu
             */
            $menu = $this->menuFactory->create();
            $errors = $menu->validate($request, $store, $id);
            if (!empty($errors)) {
                $response->setError(1);
                $response->setMessages($errors);
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $response->setError(1);
            $response->setMessages([$e->getMessage()]);
        }
        return $this->resultJsonFactory->create()->setData($response);
    }
}
