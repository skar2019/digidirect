<?php

namespace Ewave\StyleGuide\Controller\Index;

class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * ResultPageFactory
     *
     * @var ResultPageFactory
     */
    protected $resultPageFactory;

    /**
     * HelperData
     *
     * @var HelperData
     */
    protected $helperData;

    /**
     * Data Constructor
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Ewave\StyleGuide\Helper\Data $helperData
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Ewave\StyleGuide\Helper\Data $helperData
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->helperData = $helperData;
    }

    /**
     * Output page if 'separate page' enabled in back office
     */
    public function execute()
    {
        $whereToShow = $this->helperData->getShowMode();
        if ($whereToShow === 'disable') {
            $this->_forward('defaultNoRoute');
        } else {
            $result = $this->resultPageFactory->create();
            return $result;
        }
    }
}
