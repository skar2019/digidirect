<?php
namespace Digidirect\Faq\Controller;

/**
 * Class Router
 * @package Digidirect\Faq\Controller
 */
class Router implements \Magento\Framework\App\RouterInterface
{
    /**
     * @var \Magento\Framework\App\ActionFactory
     */
    protected $actionFactory;

    /**
     * Response
     *
     * @var \Magento\Framework\App\ResponseInterface
     */
    protected $_response;

    /**
     * @var \Digidirect\Faq\Helper\Data
     */
    protected $_faqHelper;

    /**
     * Router constructor.
     * @param \Magento\Framework\App\ActionFactory $actionFactory
     * @param \Magento\Framework\App\ResponseInterface $response
     * @param \Digidirect\Faq\Helper\Data $faqHelper
     */
    public function __construct(
        \Magento\Framework\App\ActionFactory $actionFactory,
        \Magento\Framework\App\ResponseInterface $response,
        \Digidirect\Faq\Helper\Data $faqHelper
    ) {
        $this->actionFactory = $actionFactory;
        $this->_response = $response;
        $this->_faqHelper = $faqHelper;
    }

    /**
     * Validate and Match
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return bool
     */
    public function match(\Magento\Framework\App\RequestInterface $request)
    {
       
        $identifier = trim($request->getPathInfo(), '/');
        if ($this->_faqHelper->isModuleEnabled() && $this->_faqHelper->isValidUrl($identifier)) {
            $request->setModuleName('faq')->setControllerName('index')->setActionName('index');
        } else {
            return null;
        }
        /*
         * We have match and now we will forward action
         */
        return $this->actionFactory->create(
            'Magento\Framework\App\Action\Forward',
            ['request' => $request]
        );
    }
}
