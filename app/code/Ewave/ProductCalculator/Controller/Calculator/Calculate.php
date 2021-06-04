<?php

namespace Ewave\ProductCalculator\Controller\Calculator;

use Ewave\ProductCalculator\Api\CalculatorRepositoryInterface;
use Ewave\ProductCalculator\Block\Calculator\ProductList;
use Ewave\ProductCalculator\Model\Constants;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Registry;

/**
 * Class Calculate
 * @package Ewave\ProductCalculator\Controller\Calculator
 */
class Calculate extends Action
{
    /**
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * @var CalculatorRepositoryInterface
     */
    protected $calculatorRepository;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @param Registry $coreRegistry
     * @param CalculatorRepositoryInterface $calculatorRepository
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param Context $context
     */
    public function __construct(
        Registry $coreRegistry,
        CalculatorRepositoryInterface $calculatorRepository,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        Context $context
    ) {
        parent::__construct($context);
        $this->coreRegistry = $coreRegistry;
        $this->calculatorRepository = $calculatorRepository;
        $this->resultJsonFactory = $resultJsonFactory;
    }

    /**
     * @return $this|void
     */
    public function execute()
    {
        if (!$this->getRequest()->isAjax()) {
            $this->_forward('noroute');

            return;
        }
        try {
            if ($calculatorId = $this->getRequest()->getParam('calculator_id')) {
                $calculator = $this->calculatorRepository->getById($calculatorId);
                $this->coreRegistry->register(Constants::CURRENT_CALCULATOR, $calculator);
            }
            $this->_view->loadLayout();
            $productListBlock = $this->_view->getLayout()->getBlock(ProductList::NAME_IN_LAYOUT);
            if ($productListBlock) {
                $result = $productListBlock->toHtml();
            } else {
                $result['error'] = __('Error while rendering products');
            }
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
        }

        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setData($result);
    }
}
