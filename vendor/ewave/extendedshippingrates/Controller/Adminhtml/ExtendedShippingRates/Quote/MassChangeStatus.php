<?php
namespace Ewave\ExtendedShippingRates\Controller\Adminhtml\ExtendedShippingRates\Quote;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Ewave\ExtendedShippingRates\Model\ResourceModel\Rule\CollectionFactory;
use Ewave\ExtendedShippingRates\Model\RuleFactory;
use Ewave\ExtendedShippingRates\Api\RuleRepositoryInterface;

class MassChangeStatus extends Action
{
    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var string
     */
    protected $redirectUrl = '*/*/index';

    /**
     * @var CollectionFactory
     */
    protected $ruleCollectionFactory;

    /**
     * @var RuleFactory
     */
    protected $ruleFactory;

    /**
     * @var RuleRepositoryInterface
     */
    protected $ruleRepository;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $ruleCollectionFactory
     * @param RuleFactory $ruleFactory
     * @param RuleRepositoryInterface $ruleRepository
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $ruleCollectionFactory,
        RuleFactory $ruleFactory,
        RuleRepositoryInterface $ruleRepository
    ) {
        parent::__construct($context);
        $this->ruleCollectionFactory = $ruleCollectionFactory;
        $this->filter = $filter;
        $this->ruleFactory = $ruleFactory;
        $this->ruleRepository = $ruleRepository;
    }

    /**
     * Update rule's is active status
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        try {
            $collection = $this->filter->getCollection($this->ruleCollectionFactory->create());
            $updatedRulesCount = 0;
            foreach ($collection->getAllIds() as $ruleId) {
                $rule = $this->ruleRepository->getById($ruleId);
                $rule->setData('is_active', $this->getRequest()->getParam('is_active'));
                $this->ruleRepository->save($rule);
                $updatedRulesCount++;
            }

            if ($updatedRulesCount) {
                $this->messageManager->addSuccessMessage(__('A total of %1 record(s) were updated.', $updatedRulesCount));
            }

            /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultFactory
                ->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setPath('ewave_extendedshippingrates/extendedshippingrates_quote/index');

            return $resultRedirect;
        } catch (\Exception $e) {
            /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
            $this->messageManager->addErrorMessage($e->getMessage());
            $resultRedirect = $this->resultFactory
                ->create(ResultFactory::TYPE_REDIRECT);

            return $resultRedirect->setPath($this->redirectUrl);
        }
    }

    /**
     * Returns result of current user permission check on resource and privilege
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Ewave_ExtendedShippingRates::quote');
    }
}
