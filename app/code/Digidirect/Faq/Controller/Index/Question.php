<?php

namespace Digidirect\Faq\Controller\Index;

use Digidirect\Faq\Api\FaqRepositoryInterface;
use Digidirect\Faq\Model\Email\Admin;
use Digidirect\Faq\Model\Faq;
use Digidirect\Faq\Model\FaqFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Filter\RemoveTags;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Question
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Question extends Action
{
    /**
     * @var \Digidirect\Faq\Model\ResourceModel\FaqRepository
     */
    protected $faqRepository;

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var FaqFactory
     */
    protected $faqFactory;

    /**
     * @var Admin
     */
    protected $adminEmailModel;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var RemoveTags
     */
    protected $escaper;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Question constructor.
     *
     * @param Context $context
     * @param FaqRepositoryInterface $faqRepository
     * @param DataPersistorInterface $dataPersistor
     * @param FaqFactory $faqFactory
     * @param Admin $adminEmailModel
     * @param ScopeConfigInterface $scopeConfig
     * @param RemoveTags $removeTags
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        FaqRepositoryInterface $faqRepository,
        DataPersistorInterface $dataPersistor,
        FaqFactory $faqFactory,
        Admin $adminEmailModel,
        ScopeConfigInterface $scopeConfig,
        RemoveTags $removeTags,
        StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->faqRepository = $faqRepository;
        $this->dataPersistor = $dataPersistor;
        $this->faqFactory = $faqFactory;
        $this->adminEmailModel = $adminEmailModel;
        $this->scopeConfig = $scopeConfig;
        $this->escaper = $removeTags;
        $this->storeManager = $storeManager;
    }

    /**
     * @return $this|void
     */
    public function execute()
    {
        $redirectFactory = $this->resultRedirectFactory->create();
        $post = $this->getRequest()->getPostValue();
        if (!$post) {
            $this->_redirect('*/*/');
            return;
        }
        try {
            $postObject = new DataObject();

            $error = false;

            $this->_eventManager->dispatch('faq_question_validate_before', ['post_value' => $post]);

            foreach ($post as $key => $value) {
                if (is_string($value)) {
                    $post[$key] = $this->escaper->filter($value);
                }
            }

            $postObject->setData($post);

            if (!\Zend_Validate::is(trim($post['name']), 'NotEmpty')) {
                $error = true;
            }
            if (!\Zend_Validate::is(trim($post['question']), 'NotEmpty')) {
                $error = true;
            }
            if (!\Zend_Validate::is(trim($post['customer_email']), 'EmailAddress')) {
                $error = true;
            }
            if ($error) {
                throw new \Exception();
            }

            $faq = $this->faqFactory->create();
            $postObject->setData(
                'category_id',
                $this->scopeConfig->getValue(
                    'digidirect_faq/customer_questions/category_id',
                    ScopeInterface::SCOPE_STORE
                )
            );

            $postObject->setQuestion('Customer Question: ' . $post['question']);
            $postObject->setAnswer('');
            $postObject->setFromStore($this->storeManager->getStore()->getId());
            $postObject->setStatus(Faq::STATUS_INACTIVE);
            /**
             * @var $faq Faq
             */
            $faq->setData($postObject->getData());
            $this->faqRepository->save($faq);

            $this->adminEmailModel->sendEmail($postObject);

            $this->messageManager->addSuccessMessage(
                __('You submitted a question successfully.')
            );
            $this->dataPersistor->clear(\Digidirect\Faq\Helper\Question::POST_VALUE_KEY);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(
                __('We can\'t process your request right now. Sorry, that\'s all we know.' . $e->getMessage())
            );
            $this->dataPersistor->set(\Digidirect\Faq\Helper\Question::POST_VALUE_KEY, $post);
        }

        return $redirectFactory->setPath('faq/index/index');
    }
}
