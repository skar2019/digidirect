<?php
namespace Ewave\MyStoreWidget\Plugin\Magento\App\Action;

use Ewave\MyStoreWidget\Helper\Data as Helper;

/**
 * Class ContextPlugin
 */
class ContextPlugin
{
    /**
     * @var Helper
     */
    protected $helper;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * @param Helper $helper
     * @param \Magento\Framework\App\Http\Context $httpContext
     */
    public function __construct(
        Helper $helper,
        \Magento\Framework\App\Http\Context $httpContext
    ) {
        $this->helper = $helper;
        $this->httpContext = $httpContext;
    }

    /**
     * @param \Magento\Framework\App\ActionInterface $subject
     * @param \Closure $proceed
     * @param \Magento\Framework\App\RequestInterface $request
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundDispatch(
        \Magento\Framework\App\ActionInterface $subject,
        \Closure $proceed,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $currentStore = $this->helper->getCurrentStore();
        $this->httpContext->setValue(
            Helper::MYSTORE_COOKIE_NAME,
            ($currentStore ? $currentStore->getId() : false),
            false
        );
        return $proceed($request);
    }
}
