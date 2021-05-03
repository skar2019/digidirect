<?php
namespace Digidirect\Faq\Controller\Index;

use Digidirect\Faq\Api\CategoryRepositoryInterface;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class Ajaxview
 * @package Digidirect\Faq\Controller\Index
 */
class Ajaxview extends AbstractAction
{
    /**
     * @var ResultFactory
     */
    protected $resultFactory;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var \Magento\Framework\App\Response\Http
     */
    protected $httpResponse;

    /**
     * @var int
     */
    protected $cacheTtl;

    /**
     * Ajaxview constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param CategoryRepositoryInterface $categoryRepository
     * @param \Magento\Framework\App\Response\Http $httpResponse
     * @param int $cacheTtl
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        CategoryRepositoryInterface $categoryRepository,
        \Magento\Framework\App\Response\Http $httpResponse,
        $cacheTtl = 604800
    ) {
        parent::__construct($context, $categoryRepository);
        $this->resultFactory = $context->getResultFactory();
        $this->resultJsonFactory = $resultJsonFactory;
        $this->httpResponse = $httpResponse;
        $this->cacheTtl = $cacheTtl;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        /** @var \Magento\Framework\View\Result\Layout $resultLayout */
        $resultLayout = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $this->processLayoutUpdate($resultLayout);
        $block = $resultLayout->getLayout()->getBlock('digidirect.faq.listfaq');

        $this->httpResponse->setPublicHeaders($this->cacheTtl);

        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setData(['content' => $block->toHtml()]);
    }
}
