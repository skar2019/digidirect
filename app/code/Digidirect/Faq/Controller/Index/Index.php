<?php
namespace Digidirect\Faq\Controller\Index;

use Digidirect\Faq\Api\CategoryRepositoryInterface;
use Magento\Framework\Exception\NotFoundException;

/**
 * Class Index
 * @package Digidirect\Faq\Controller\Index
 */
class Index extends AbstractAction
{
    /**
     * @var \Digidirect\Faq\Helper\Data
     */
    protected $faqHelper;

    /**
     * Index constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Digidirect\Faq\Helper\Data $faqHelper
     * @param CategoryRepositoryInterface $categoryRepository
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Digidirect\Faq\Helper\Data $faqHelper,
        CategoryRepositoryInterface $categoryRepository
    ) {
        $this->faqHelper = $faqHelper;
        parent::__construct($context, $categoryRepository);
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     * @throws NotFoundException
     */
    public function execute()
    {
        if (!$this->faqHelper->isModuleEnabled()) {
            throw new NotFoundException(__('Page not found.'));
        }
        $resultPage = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_PAGE);
        $this->processLayoutUpdate($resultPage);
        return $resultPage;
    }
}
